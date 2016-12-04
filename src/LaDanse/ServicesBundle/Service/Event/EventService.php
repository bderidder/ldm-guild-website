<?php

namespace LaDanse\ServicesBundle\Service\Event;

use Doctrine\Bundle\DoctrineBundle\Registry;
use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\ServicesBundle\Common\LaDanseService;
use LaDanse\ServicesBundle\Service\DTO\Event\PutEventState;
use LaDanse\ServicesBundle\Service\Event\Command\PutEventStateCommand;
use LaDanse\ServicesBundle\Service\Event\Command\PostSignUpCommand;
use LaDanse\ServicesBundle\Service\Event\Command\NotifyEventTodayCommand;
use LaDanse\ServicesBundle\Service\Event\Command\PostEventCommand;
use LaDanse\ServicesBundle\Service\Event\Command\PutEventCommand;
use LaDanse\ServicesBundle\Service\Event\Command\DeleteEventCommand;
use LaDanse\ServicesBundle\Service\Event\Command\PutSignUpCommand;
use LaDanse\ServicesBundle\Service\Event\Command\DeleteSignUpCommand;
use LaDanse\ServicesBundle\Service\Event\Query\GetAllEventsPagedQuery;
use LaDanse\ServicesBundle\Service\Event\Query\GetEventByIdQuery;
use Symfony\Component\DependencyInjection\ContainerInterface;
use LaDanse\ServicesBundle\Service\DTO as DTO;

/**
 * Class EventService
 * @package LaDanse\ServicesBundle\Service\Event
 *
 * @DI\Service(EventService::SERVICE_NAME, public=true)
 */
class EventService extends LaDanseService
{
    const SERVICE_NAME = 'LaDanse.EventService';

    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    public $logger;

    /**
     * @var $doctrine Registry
     * @DI\Inject("doctrine")
     */
    public $doctrine;

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
     * Return all events.
     *
     * The result is sorted by invite time (ascending) and limited to 28 days starting from $fromTime (included)
     *
     * @param \DateTime $fromTime
     *
     * @return DTO\Event\EventPage
     */
    public function getAllEventsPaged(\DateTime $fromTime)
    {
        /** @var GetAllEventsPagedQuery $query */
        $query = $this->get(GetAllEventsPagedQuery::SERVICE_NAME);

        $query->setStartOn($fromTime);

        return $query->run();
    }

    /**
     * Return the event with the given id
     *
     * @param int $eventId id of event to retrieve
     *
     * @throws EventDoesNotExistException if the event does not exist
     *
     * @return DTO\Event\Event
     */
    public function getEventById($eventId)
    {
        /** @var GetEventByIdQuery $query */
        $query = $this->container->get(GetEventByIdQuery::SERVICE_NAME);

        $query->setEventId($eventId);

        return $query->run();
    }

    /**
     * Create a new event
     *
     * @param DTO\Event\PostEvent $postEvent
     */
    public function postEvent(DTO\Event\PostEvent $postEvent)
    {
        /** @var PostEventCommand $command */
        $command = $this->container->get(PostEventCommand::SERVICE_NAME);

        $command->setPostEventDto($postEvent);

        return $command->run();
    }

    /**
     * Update an existing event
     *
     * @param int $eventId
     * @param DTO\Event\PutEvent $putEvent
     */
    public function putEvent(int $eventId, DTO\Event\PutEvent $putEvent)
    {
        /** @var PutEventCommand $command */
        $command = $this->container->get(PutEventCommand::SERVICE_NAME);

        $command->setEventId($eventId);
        $command->setPutEventDto($putEvent);

        return $command->run();
    }

    /**
     * Update the state of an existing event
     *
     * @param int $eventId
     * @param PutEventState $putEventState
     */
    public function putEventState($eventId, DTO\Event\PutEventState $putEventState)
    {
        /** @var PutEventStateCommand $command */
        $command = $this->container->get(PutEventStateCommand::SERVICE_NAME);

        $command->setEventId($eventId);
        $command->setPutEventState($putEventState);

        return $command->run();
    }

    /**
     * Delete an existing event
     *
     * @param $eventId
     */
    public function deleteEvent($eventId)
    {
        /** @var DeleteEventCommand $command */
        $command = $this->container->get(DeleteEventCommand::SERVICE_NAME);

        $command->setEventId($eventId);

        $command->run();
    }

    /**
     * Create a new sign up for an existing event
     *
     * @param $eventId
     * @param DTO\Event\PostSignUp $postSignUp
     */
    public function postSignUp($eventId, DTO\Event\PostSignUp $postSignUp)
    {
        /** @var PostSignUpCommand $command */
        $command = $this->container->get(PostSignUpCommand::SERVICE_NAME);

        $command->setEventId($eventId);
        $command->setPostSignUp($postSignUp);

        return $command->run();
    }

    /**
     * Update an existing sign up
     *
     * @param $eventId
     * @param $signUpId
     * @param DTO\Event\PutSignUp $putSignUp
     */
    public function putSignUp($eventId, $signUpId, DTO\Event\PutSignUp $putSignUp)
    {
        /** @var PutSignUpCommand $command */
        $command = $this->container->get(PutSignUpCommand::SERVICE_NAME);

        $command->setEventId($eventId);
        $command->setSignUpId($signUpId);
        $command->setPutSignUp($putSignUp);

        return $command->run();
    }

    /**
     * Remove an existing sign up
     *
     * @param $eventId
     * @param $signUpId
     */
    public function deleteSignUp($eventId, $signUpId)
    {
        /** @var DeleteSignUpCommand $command */
        $command = $this->container->get(DeleteSignUpCommand::SERVICE_NAME);

        $command->setEventId($eventId);
        $command->setSignUpId($signUpId);

        return $command->run();
    }

    /**
     * Create notification events for all events that happen today
     */
    public function notifyEventsToday()
    {
        /** @var NotifyEventTodayCommand $notifyEventTodayCommand */
        $notifyEventTodayCommand = $this->container->get(NotifyEventTodayCommand::SERVICE_NAME);

        $notifyEventTodayCommand->run();
    }
}