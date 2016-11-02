<?php

namespace LaDanse\ServicesBundle\Service\Event\Command;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\Account;
use LaDanse\DomainBundle\Entity\Event;
use LaDanse\DomainBundle\Entity\ForRole;
use LaDanse\DomainBundle\Entity\SignUp;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Common\AbstractCommand;
use LaDanse\ServicesBundle\Service\Event\EventDoesNotExistException;
use LaDanse\ServicesBundle\Service\Event\EventInThePastException;
use LaDanse\ServicesBundle\Service\Event\EventInvalidStateChangeException;
use LaDanse\ServicesBundle\Service\Event\UserAlreadySignedException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @DI\Service(CreateSignUpCommand::SERVICE_NAME, public=true, shared=false)
 */
class CreateSignUpCommand extends AbstractCommand
{
    const SERVICE_NAME = 'LaDanse.CreateSignUpCommand';

    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    public $logger;

    /**
     * @var $eventDispatcher EventDispatcherInterface
     * @DI\Inject("event_dispatcher")
     */
    public $eventDispatcher;

    /**
     * @var $logger \Doctrine\Bundle\DoctrineBundle\Registry
     * @DI\Inject("doctrine")
     */
    public $doctrine;

    /** @var $eventId int  */
    private $eventId;
    /** @var $account Account */
    private $account;
    /** @var $signUpType string */
    private $signUpType;
    /** @var $roles array */
    private $roles;

    /** @var $event Event */
    private $cachedEvent;

    /**
     * @param ContainerInterface $container
     *
     * @DI\InjectParams({
     *     "container" = @DI\Inject("service_container")
     * })
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

    /**
     * @return int
     */
    public function getEventId()
    {
        return $this->eventId;
    }

    /**
     * @param int $eventId
     */
    public function setEventId($eventId)
    {
        $this->eventId = $eventId;
    }

    /**
     * @return Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param Account $account
     */
    public function setAccount($account)
    {
        $this->account = $account;
    }

    /**
     * @return string
     */
    public function getSignUpType()
    {
        return $this->signUpType;
    }

    /**
     * @param string $signUpType
     */
    public function setSignUpType($signUpType)
    {
        $this->signUpType = $signUpType;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    /**
     * @return Event
     */
    protected function getCachedEvent()
    {
        return $this->cachedEvent;
    }

    /**
     * @param Event $cachedEvent
     */
    protected function setCachedEvent($cachedEvent)
    {
        $this->cachedEvent = $cachedEvent;
    }

    protected function validateInput()
    {
        $em = $this->doctrine->getManager();

        /* @var $repository \Doctrine\ORM\EntityRepository */
        $repository = $em->getRepository(Event::REPOSITORY);
        /* @var $event \LaDanse\DomainBundle\Entity\Event */
        $event = $repository->find($this->getEventId());

        if (is_null($event))
        {
            throw new EventDoesNotExistException('Event does not exist');
        }

        $this->setCachedEvent($event);

        $fsm = $event->getStateMachine();

        if (!($fsm->getCurrentState() == 'Pending' || $fsm->getCurrentState() == 'Confirmed'))
        {
            throw new EventInvalidStateChangeException(
                'The event is not in Pending or Confirmed state, sign-up creation is not allowed'
            );
        }

        if ($this->isCurrentUserSigned($event))
        {
            throw new UserAlreadySignedException('User has already signed to this event');
        }

        $currentDateTime = new \DateTime();
        if ($event->getInviteTime() <= $currentDateTime)
        {
            throw new EventInThePastException('Event is in the past, sign ups not allowed anymore');
        }
    }

    protected function runCommand()
    {
        $em = $this->doctrine->getManager();

        $signUp = new SignUp();
        $signUp->setEvent($this->getCachedEvent());
        $signUp->setType($this->getSignUpType());
        $signUp->setAccount($this->getAccount());

        foreach($this->getRoles() as $strForRole)
        {
            $forRole = new ForRole();

            $forRole->setSignUp($signUp);
            $forRole->setRole($strForRole);

            $signUp->addRole($forRole);

            $em->persist($forRole);
        }

        $this->logger->info(__CLASS__ . ' persisting new sign up');

        $em->persist($signUp);
        $em->flush();

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::SIGNUP_CREATE,
                $this->getAccount(),
                [
                    'event'  => $this->getCachedEvent()->toJson(),
                    'signUp' => $signUp->toJson()
                ]
            )
        );
    }

    private function isCurrentUserSigned(Event $event)
    {
        $account = $this->getAccount();

        $em = $this->doctrine->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery('SELECT s ' .
            'FROM LaDanse\DomainBundle\Entity\SignUp s ' .
            'WHERE s.event = :event AND s.account = :account');
        $query->setParameter('account', $account->getId());
        $query->setParameter('event', $event->getId());

        $signUps = $query->getResult();

        if (count($signUps) === 0)
        {
            return NULL;
        }
        else
        {
            return $signUps[0];
        }
    }
}