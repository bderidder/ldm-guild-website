<?php

namespace LaDanse\ServicesBundle\Service\Event\Command;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\FSM\EventStateMachine;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Common\AbstractCommand;
use LaDanse\ServicesBundle\Common\InvalidInputException;
use LaDanse\ServicesBundle\Service\DTO\Event\PostSignUp;
use LaDanse\ServicesBundle\Service\Event\EventDoesNotExistException;
use LaDanse\ServicesBundle\Service\Event\EventInThePastException;
use LaDanse\ServicesBundle\Service\Event\EventInvalidStateChangeException;
use LaDanse\ServicesBundle\Service\Event\EventService;
use LaDanse\ServicesBundle\Service\Event\UserAlreadySignedException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use LaDanse\DomainBundle\Entity as Entity;
use LaDanse\ServicesBundle\Service\DTO as DTO;

/**
 * @DI\Service(PostSignUpCommand::SERVICE_NAME, public=true, shared=false)
 */
class PostSignUpCommand extends AbstractCommand
{
    const SERVICE_NAME = 'LaDanse.PostSignUpCommand';

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

    /**
     * @var EventService $eventService
     * @DI\Inject(EventService::SERVICE_NAME)
     */
    public $eventService;

    /** @var $eventId int  */
    private $eventId;

    /** @var PostSignUp */
    private $postSignUp;

    /** @var DTO\Event\Event $cachedEventDto */
    private $cachedEventDto;

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
     * @return PostSignUp
     */
    public function getPostSignUp(): PostSignUp
    {
        return $this->postSignUp;
    }

    /**
     * @param PostSignUp $postSignUp
     * @return PostSignUpCommand
     */
    public function setPostSignUp(PostSignUp $postSignUp): PostSignUpCommand
    {
        $this->postSignUp = $postSignUp;
        return $this;
    }

    protected function validateInput()
    {
        /** @var string $signupType */
        $signupType = $this->getPostSignUp()->getSignUpType();

        if (!($signupType == Entity\SignUpType::WILLCOME
                ||
            $signupType == Entity\SignUpType::MIGHTCOME
                ||
            $signupType == Entity\SignUpType::ABSENCE))
        {
            throw new InvalidInputException("Invalid signupType given");
        }

        if (($signupType == Entity\SignUpType::ABSENCE)
                &&
            ($this->getPostSignUp()->getRoles() != null
                ||
             count($this->getPostSignUp()->getRoles()) > 0)
        )
        {
            throw new InvalidInputException("When signing as ABSENCE, roles must be empty");
        }
    }

    protected function runCommand()
    {
        $em = $this->doctrine->getManager();

        /* @var $repository \Doctrine\ORM\EntityRepository */
        $repository = $em->getRepository(Entity\Event::REPOSITORY);

        /* @var Entity\Event $event $event */
        $event = $repository->find($this->getEventId());

        if (is_null($event))
        {
            throw new EventDoesNotExistException('Event does not exist');
        }

        $oldEventDto = $this->eventService->getEventById($this->getEventId());

        $fsm = $event->getStateMachine();

        $this->logger->info("Event has state " . $fsm->getCurrentState()->getName());
        $this->logger->info("Event state comparison " . strcmp($fsm->getCurrentState()->getName(), EventStateMachine::CONFIRMED));

        if (!(strcmp($fsm->getCurrentState()->getName(), EventStateMachine::PENDING) == 0
                ||
            strcmp($fsm->getCurrentState()->getName(), EventStateMachine::CONFIRMED) == 0))
        {
            throw new EventInvalidStateChangeException(
                'The event is not in Pending or Confirmed state, signing up is not allowed'
            );
        }

        if ($this->isUserSigned($oldEventDto, $this->getPostSignUp()->getAccountReference()))
        {
            throw new UserAlreadySignedException('User has already signed to this event');
        }

        $currentDateTime = new \DateTime();
        if ($event->getInviteTime() <= $currentDateTime)
        {
            throw new EventInThePastException('Event is in the past, sign ups not allowed anymore');
        }

        $signUp = new Entity\SignUp();
        $signUp->setEvent($event);
        $signUp->setType($this->getPostSignUp()->getSignUpType());
        $signUp->setAccount(
            $em->getReference(
                Entity\Account::class,
                $this->getPostSignUp()->getAccountReference()->getId()
            )
        );

        foreach($this->getPostSignUp()->getRoles() as $strForRole)
        {
            $forRole = new Entity\ForRole();

            $forRole->setSignUp($signUp);
            $forRole->setRole($strForRole);

            $signUp->addRole($forRole);

            $em->persist($forRole);
        }

        $this->logger->info(__CLASS__ . ' persisting new sign up');

        $em->persist($signUp);
        $em->flush();

        $newEventDto = $this->eventService->getEventById($this->getEventId());

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::SIGNUP_CREATE,
                $this->getAccount(),
                [
                    'oldEvent' => ActivityEvent::annotatedToSimpleObject($oldEventDto),
                    'newEvent' => ActivityEvent::annotatedToSimpleObject($newEventDto)
                ]
            )
        );

        return $newEventDto;
    }

    private function isUserSigned(DTO\Event\Event $eventDto, DTO\Reference\IntegerReference $accountReference)
    {
        foreach($eventDto->getSignUps() as $signUp)
        {
            /** @var DTO\Event\SignUp $signUp */
            if ($signUp->getAccount()->getId() == $accountReference->getId())
            {
                return true;
            }
        }

        return false;
    }
}