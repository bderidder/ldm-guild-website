<?php

namespace LaDanse\ServicesBundle\Service\Event\Command;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity as Entity;
use LaDanse\DomainBundle\Entity\ForRole;
use LaDanse\DomainBundle\Entity\SignUp;
use LaDanse\DomainBundle\Entity\SignUpType;
use LaDanse\DomainBundle\FSM\EventStateMachine;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Common\AbstractCommand;
use LaDanse\ServicesBundle\Common\InvalidInputException;
use LaDanse\ServicesBundle\Service\Authorization\AuthorizationService;
use LaDanse\ServicesBundle\Service\Authorization\NotAuthorizedException;
use LaDanse\ServicesBundle\Service\Authorization\ResourceByValue;
use LaDanse\ServicesBundle\Service\Authorization\SubjectReference;
use LaDanse\ServicesBundle\Service\DTO\Event\PutSignUp;
use LaDanse\ServicesBundle\Service\Event\EventDoesNotExistException;
use LaDanse\ServicesBundle\Service\Event\EventInThePastException;
use LaDanse\ServicesBundle\Service\Event\EventInvalidStateChangeException;
use LaDanse\ServicesBundle\Service\Event\EventService;
use LaDanse\ServicesBundle\Service\Event\SignUpDoesNotExistException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use LaDanse\ServicesBundle\Service\DTO as DTO;

/**
 * @DI\Service(PutSignUpCommand::SERVICE_NAME, public=true, shared=false)
 */
class PutSignUpCommand extends AbstractCommand
{
    const SERVICE_NAME = 'LaDanse.PutSignUpCommand';

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
     * @var AuthorizationService $authzService
     * @DI\Inject(AuthorizationService::SERVICE_NAME)
     */
    public $authzService;

    /**
     * @var EventService $eventService
     * @DI\Inject(EventService::SERVICE_NAME)
     */
    public $eventService;

    /** @var int $eventId */
    private $eventId;

    /** @var int $signUpId */
    private $signUpId;

    /** @var PutSignUp */
    private $putSignUp;

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
    public function getEventId(): int
    {
        return $this->eventId;
    }

    /**
     * @param int $eventId
     * @return PutSignUpCommand
     */
    public function setEventId(int $eventId): PutSignUpCommand
    {
        $this->eventId = $eventId;
        return $this;
    }

    /**
     * @return int
     */
    public function getSignUpId(): int
    {
        return $this->signUpId;
    }

    /**
     * @param int $signUpId
     * @return PutSignUpCommand
     */
    public function setSignUpId(int $signUpId): PutSignUpCommand
    {
        $this->signUpId = $signUpId;
        return $this;
    }

    /**
     * @return PutSignUp
     */
    public function getPutSignUp(): PutSignUp
    {
        return $this->putSignUp;
    }

    /**
     * @param PutSignUp $putSignUp
     * @return PutSignUpCommand
     */
    public function setPutSignUp(PutSignUp $putSignUp): PutSignUpCommand
    {
        $this->putSignUp = $putSignUp;
        return $this;
    }

    protected function validateInput()
    {
        /** @var string $signupType */
        $signupType = $this->getPutSignUp()->getSignUpType();

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
            ($this->getPutSignUp()->getRoles() != null
                ||
             count($this->getPutSignUp()->getRoles()) > 0)
        )
        {
            throw new InvalidInputException("When signing as ABSENCE, roles must be empty");
        }
    }

    protected function runCommand()
    {
        $em = $this->doctrine->getManager();

        /** @var \Doctrine\ORM\EntityRepository $signUpRepository */
        $signUpRepository = $em->getRepository(SignUp::REPOSITORY);

        /** @var SignUp $signUp */
        $signUp = $signUpRepository->find($this->getSignUpId());

        if (is_null($signUp))
        {
            throw new SignUpDoesNotExistException('Sign-up does not exist');
        }

        $event = $signUp->getEvent();

        if ($event->getId() != $this->getEventId())
        {
            throw new EventDoesNotExistException('Event does not exist');
        }

        $oldEventDto = $this->eventService->getEventById($this->getEventId());

        $this->authzService->allowOrThrow(
            new SubjectReference($this->getAccount()),
            ActivityType::SIGNUP_EDIT,
            new ResourceByValue(
                DTO\Event\SignUp::class,
                $oldEventDto->getSignUpForId($this->getSignUpId())
            )
        );

        $fsm = $event->getStateMachine();

        if (!(strcmp($fsm->getCurrentState()->getName(), EventStateMachine::PENDING) == 0
                ||
            strcmp($fsm->getCurrentState()->getName(), EventStateMachine::CONFIRMED) == 0))
        {
            throw new EventInvalidStateChangeException(
                'The event is not in Pending or Confirmed state, updating a sign up is not allowed'
            );
        }

        $currentDateTime = new \DateTime();
        if ($event->getInviteTime() <= $currentDateTime)
        {
            throw new EventInThePastException('Event is in the past, updating sign-up is not allowed anymore');
        }

        $signUp->setType($this->getPutSignUp()->getSignUpType());

        foreach($signUp->getRoles() as $origRole)
        {
            $em->remove($origRole);
        }

        $signUp->getRoles()->clear();

        if ($this->getPutSignUp()->getSignUpType() != SignUpType::ABSENCE)
        {
            foreach($this->getPutSignUp()->getRoles() as $strForRole)
            {
                $forRole = new ForRole();

                $forRole->setSignUp($signUp);
                $forRole->setRole($strForRole);

                $signUp->addRole($forRole);

                $em->persist($forRole);
            }
        }

        $this->logger->info(__CLASS__ . ' persisting new sign up');

        $em->persist($signUp);
        $em->flush();

        $newEventDto = $this->eventService->getEventById($this->getEventId());

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::SIGNUP_EDIT,
                $this->getAccount(),
                [
                    'oldEvent'  => ActivityEvent::annotatedToSimpleObject($oldEventDto),
                    'newEvent'  => ActivityEvent::annotatedToSimpleObject($newEventDto),
                    'signUpId'  => $this->getSignUpId(),
                    'putSignUp' => ActivityEvent::annotatedToSimpleObject($this->getPutSignUp())
                ]
            )
        );

        return $newEventDto;
    }
}