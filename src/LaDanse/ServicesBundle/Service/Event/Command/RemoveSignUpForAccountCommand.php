<?php

namespace LaDanse\ServicesBundle\Service\Event\Command;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\CommonBundle\Helper\AbstractCommand;
use LaDanse\DomainBundle\Entity\Event;
use LaDanse\DomainBundle\Entity\SignUp;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @DI\Service(RemoveSignUpCommand::SERVICE_NAME, public=true, scope="prototype")
 */
class RemoveSignUpForAccountCommand extends AbstractCommand
{
    const SERVICE_NAME = 'LaDanse.RemoveSignUpCommand';

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

    /** @var int $eventId */
    private $eventId;

    /** @var int $accountId */
    private $accountId;

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
     * @return RemoveSignUpForAccountCommand
     */
    public function setEventId($eventId)
    {
        $this->eventId = $eventId;

        return $this;
    }

    /**
     * @return int
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * @param int $accountId
     * @return RemoveSignUpForAccountCommand
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;

        return $this;
    }

    protected function validateInput()
    {
    }

    protected function runCommand()
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->doctrine->getManager();

        /* @var \Doctrine\ORM\EntityRepository $repository */
        $repository = $this->doctrine->getRepository(Event::REPOSITORY);

        /* @var Event $event */
        $event = $repository->find($this->getEventId());

        if ($event == null)
        {
            throw new EventDoesNotExistException("Sign up with id " . $this->getEventId() . ' does not exist');
        }

        $currentDateTime = new \DateTime();
        if ($event->getInviteTime() <= $currentDateTime)
        {
            throw new EventInThePastException("Event belonging to sign up is in the past and cannot be changed");
        }

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery('SELECT s ' .
            'FROM LaDanse\DomainBundle\Entity\SignUp s ' .
            'WHERE s.event = :event AND s.account = :account');
        $query->setParameter('account', $this->getAccountId());
        $query->setParameter('event', $this->getEventId());
        $signUps = $query->getResult();

        /** @var SignUp $signUpToRemove */
        $signUpToRemove = null;

        if (count($signUps) === 0)
        {
            throw new SignUpDoesNotExistException("Could not find sign up for given account and event");
        }
        else
        {
            $signUpToRemove = $signUps[0];

            $em->remove($signUpToRemove);
        }

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::SIGNUP_DELETE,
                $this->getAccount(),
                array(
                    'event'  => $signUpToRemove->getEvent()->toJson(),
                    'signUp' => $signUpToRemove->toJson()
                ))
        );

        $em->flush();
    }
}