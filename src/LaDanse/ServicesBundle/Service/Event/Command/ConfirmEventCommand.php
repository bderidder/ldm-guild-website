<?php

namespace LaDanse\ServicesBundle\Service\Event\Command;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\Event;
use LaDanse\DomainBundle\FSM\EventStateMachine;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Common\AbstractCommand;
use LaDanse\ServicesBundle\Service\Authorization\AuthorizationService;
use LaDanse\ServicesBundle\Service\Authorization\NotAuthorizedException;
use LaDanse\ServicesBundle\Service\Authorization\ResourceByValue;
use LaDanse\ServicesBundle\Service\Authorization\SubjectReference;
use LaDanse\ServicesBundle\Service\Event\EventDoesNotExistException;
use LaDanse\ServicesBundle\Service\Event\EventInvalidStateChangeException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @DI\Service(ConfirmEventCommand::SERVICE_NAME, public=true, shared=false)
 */
class ConfirmEventCommand extends AbstractCommand
{
    const SERVICE_NAME = 'LaDanse.ConfirmEventCommand';

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

    protected function validateInput()
    {
    }

    protected function runCommand()
    {
        $em = $this->doctrine->getManager();

        /* @var $repository \Doctrine\ORM\EntityRepository */
        $repository = $this->doctrine->getRepository(Event::REPOSITORY);

        /* @var $event \LaDanse\DomainBundle\Entity\Event */
        $event = $repository->find($this->getEventId());

        if ($event == null)
        {
            throw new EventDoesNotExistException("Event does not exist " . $this->getEventId());
        }

        /* verify that the user can edit this particular event */
        if (!$this->authzService->evaluate(
            new SubjectReference($this->getAccount()),
            ActivityType::EVENT_CONFIRM,
            new ResourceByValue(Event::class, $event->getId(), $event)))
        {
            $this->logger->warning(__CLASS__ . ' the user is not authorized to confirm event',
                array(
                    "account" => $this->getAccount()->getId(),
                    "event" => $this->getEventId()
                )
            );

            throw new NotAuthorizedException("Current user is not allowed to confirm event");
        }

        if ($event->getStateMachine()->can(EventStateMachine::TR_CONFIRM))
        {
            $event->getStateMachine()->apply(EventStateMachine::TR_CONFIRM);
        }
        else
        {
            throw new EventInvalidStateChangeException('The event does not allow the Confirm transition in this state');
        }

        $eventJson = $event->toJson();

        $this->logger->info(__CLASS__ . ' confirming event');

        $em->flush();

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::EVENT_CONFIRM,
                $this->getAccount(),
                array(
                    'event' => $eventJson
                )
            )
        );
    }
}