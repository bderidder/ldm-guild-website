<?php

namespace LaDanse\ServicesBundle\Service\Event\Command;

use Finite\StateMachine\StateMachineInterface;
use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity as Entity;
use LaDanse\ServicesBundle\Service\DTO as DTO;
use LaDanse\DomainBundle\FSM\EventStateMachine;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Common\AbstractCommand;
use LaDanse\ServicesBundle\Service\Authorization\AuthorizationService;
use LaDanse\ServicesBundle\Service\Authorization\ResourceByValue;
use LaDanse\ServicesBundle\Service\Authorization\SubjectReference;
use LaDanse\ServicesBundle\Service\Event\EventDoesNotExistException;
use LaDanse\ServicesBundle\Service\Event\EventInvalidStateChangeException;
use LaDanse\ServicesBundle\Service\Event\EventService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @DI\Service(PutEventStateCommand::SERVICE_NAME, public=true, shared=false)
 */
class PutEventStateCommand extends AbstractCommand
{
    const SERVICE_NAME = 'LaDanse.PutEventStateCommand';

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

    /** @var int $eventId */
    private $eventId;

    /** @var DTO\Event\PutEventState */
    private $putEventState;

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
     * @return DTO\Event\PutEventState
     */
    public function getPutEventState(): DTO\Event\PutEventState
    {
        return $this->putEventState;
    }

    /**
     * @param DTO\Event\PutEventState $putEventState
     * @return PutEventStateCommand
     */
    public function setPutEventState(DTO\Event\PutEventState $putEventState): PutEventStateCommand
    {
        $this->putEventState = $putEventState;
        return $this;
    }

    protected function validateInput()
    {
    }

    protected function runCommand()
    {
        $em = $this->doctrine->getManager();

        /** @var EventService $eventService */
        $eventService = $this->container->get(EventService::SERVICE_NAME);

        $oldEventDto = $eventService->getEventById($this->getEventId());

        if ($oldEventDto == null)
        {
            throw new EventDoesNotExistException("Event does not exist " . $this->getEventId());
        }

        $this->authzService->allowOrThrow(
            new SubjectReference($this->getAccount()),
            ActivityType::EVENT_PUT_STATE,
            new ResourceByValue(DTO\Event\Event::class, $oldEventDto)
        );

        /* @var $repository \Doctrine\ORM\EntityRepository */
        $repository = $this->doctrine->getRepository(Entity\Event::REPOSITORY);

        /* @var $event \LaDanse\DomainBundle\Entity\Event */
        $event = $repository->find($this->getEventId());

        $desiredStateTransition = $this->getStateTransition($event->getStateMachine(), $this->getPutEventState()->getState());

        if (($desiredStateTransition != null) && $event->getStateMachine()->can($desiredStateTransition))
        {
            $event->getStateMachine()->apply($desiredStateTransition);
        }
        else
        {
            throw new EventInvalidStateChangeException('The event does not allow a transition to the requested state');
        }

        $this->logger->info(__CLASS__ . ' changing state of event');

        $em->flush();

        $eventDto = $eventService->getEventById($event->getId());

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::EVENT_PUT_STATE,
                $this->getAccount(),
                [
                    'oldEevent' => ActivityEvent::annotatedToSimpleObject($oldEventDto),
                    'newEvent'  => ActivityEvent::annotatedToSimpleObject($eventDto)
                ]
            )
        );

        return $eventDto;
    }

    /**
     * @param $stateMachine
     * @param string $desiredState
     *
     * @return null|string
     */
    private function getStateTransition(StateMachineInterface $stateMachine, string $desiredState)
    {
        if ($desiredState == EventStateMachine::CONFIRMED)
            return EventStateMachine::TR_CONFIRM;

        if ($desiredState == EventStateMachine::CANCELLED)
            return EventStateMachine::TR_CANCEL;

        if ($desiredState == EventStateMachine::HAPPENED)
            return EventStateMachine::TR_CONFIRM_HAPPENED;

        if ($desiredState == EventStateMachine::NOTHAPPENED)
            return EventStateMachine::TR_CONFIRM_NOT_HAPPENED;

        if ($desiredState == EventStateMachine::ARCHIVED)
            return EventStateMachine::TR_CONFIRM_NOT_HAPPENED;

        if ($desiredState == EventStateMachine::ARCHIVED)
            return EventStateMachine::TR_ARCHIVE;

        if ($desiredState == EventStateMachine::DELETED)
            return EventStateMachine::TR_DELETE;

        return null;
    }
}