<?php

namespace LaDanse\ServicesBundle\Service\Event;

use Doctrine\Bundle\DoctrineBundle\Registry;
use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\Account;
use LaDanse\DomainBundle\Entity\SignUp;
use LaDanse\ServicesBundle\Common\LaDanseService;
use LaDanse\ServicesBundle\Service\Authorization\NotAuthorizedException;
use LaDanse\ServicesBundle\Service\DTO\Event\PutEventState;
use LaDanse\ServicesBundle\Service\Event\Command\CancelEventCommand;
use LaDanse\ServicesBundle\Service\Event\Command\ConfirmEventCommand;
use LaDanse\ServicesBundle\Service\Event\Command\CreateEventCommand;
use LaDanse\ServicesBundle\Service\Event\Command\CreateSignUpCommand;
use LaDanse\ServicesBundle\Service\Event\Command\NotifyEventTodayCommand;
use LaDanse\ServicesBundle\Service\Event\Command\PostEventCommand;
use LaDanse\ServicesBundle\Service\Event\Command\PutEventCommand;
use LaDanse\ServicesBundle\Service\Event\Command\RemoveEventCommand;
use LaDanse\ServicesBundle\Service\Event\Command\RemoveSignUpCommand;
use LaDanse\ServicesBundle\Service\Event\Command\RemoveSignUpForAccountCommand;
use LaDanse\ServicesBundle\Service\Event\Command\UpdateEventCommand;
use LaDanse\ServicesBundle\Service\Event\Command\UpdateSignUpCommand;
use LaDanse\ServicesBundle\Service\Event\Query\GetAllEventsPagedQuery;
use LaDanse\ServicesBundle\Service\Event\Query\GetEventByIdQuery;
use LaDanse\ServicesBundle\Service\Event\Query\UserSignUpQuery;
use LaDanse\SiteBundle\Form\Model\SignUpFormModel;
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
     * @param DTO\Event\PutEvent $putEvent
     * @param int $eventId
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
     * @param PutEventState $putEventState
     */
    public function putEventState(DTO\Event\PutEventState $putEventState)
    {

    }

    /**
     * Delete an existing event
     *
     * @param $eventId
     */
    public function deleteEvent($eventId)
    {

    }

    /**
     * Create a new sign up for an existing event
     *
     * @param $eventId
     * @param DTO\Event\PostSignUp $postSignUp
     */
    public function postSignUp($eventId, DTO\Event\PostSignUp $postSignUp)
    {

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

    }

    /**
     * Remove an existing sign up
     *
     * @param $eventId
     * @param $signUpId
     */
    public function deleteSignUp($eventId, $signUpId)
    {

    }

    /**
     * Create a new event with the given values
     *
     * @param Account $organiser
     * @param string $name
     * @param string $description
     * @param \DateTime $inviteTime
     * @param \DateTime $startTime
     * @param \DateTime $endTIme
     */
    public function createEvent(
        Account $organiser,
        $name,
        $description,
        \DateTime $inviteTime,
        \DateTime $startTime,
        \DateTime $endTIme)
    {
        /** @var CreateEventCommand $createEventCommand */
        $createEventCommand = $this->container->get(CreateEventCommand::SERVICE_NAME);

        $createEventCommand->setOrganiser($organiser);
        $createEventCommand->setName($name);
        $createEventCommand->setDescription($description);
        $createEventCommand->setInviteTime($inviteTime);
        $createEventCommand->setStartTime($startTime);
        $createEventCommand->setEndTime($endTIme);

        $createEventCommand->run();
    }

    /**
     * Update an existing event with the new values
     *
     * @param int $eventId
     * @param string $name
     * @param string $description
     * @param \DateTime $inviteTime
     * @param \DateTime $startTime
     * @param \DateTime $endTIme
     */
    public function updateEvent(
        $eventId,
        $name,
        $description,
        \DateTime $inviteTime,
        \DateTime $startTime,
        \DateTime $endTIme)
    {
        /** @var UpdateEventCommand $updateEventCommand */
        $updateEventCommand = $this->container->get(UpdateEventCommand::SERVICE_NAME);

        $updateEventCommand->setEventId($eventId);
        $updateEventCommand->setName($name);
        $updateEventCommand->setDescription($description);
        $updateEventCommand->setInviteTime($inviteTime);
        $updateEventCommand->setStartTime($startTime);
        $updateEventCommand->setEndTime($endTIme);

        $updateEventCommand->run();
    }

    /**
     * Confirm an event that is in Pending state
     *
     * @param int $eventId
     *
     * @throws NotAuthorizedException when the currently logged in user is not allowed this action
     * @throws EventInvalidStateChangeException the event does not allow this state change right now
     * @throws EventDoesNotExistException $eventId does not point to an existing event
     */
    public function confirmEvent($eventId)
    {
        /** @var ConfirmEventCommand $confirmEventCommand */
        $confirmEventCommand = $this->container->get(ConfirmEventCommand::SERVICE_NAME);

        $confirmEventCommand->setEventId($eventId);

        $confirmEventCommand->run();
    }

    /**
     * Cancel an event that is in Pending state
     *
     * @param int $eventId
     *
     * @throws NotAuthorizedException when the currently logged in user is not allowed this action
     * @throws EventInvalidStateChangeException the event does not allow this state change right now
     * @throws EventDoesNotExistException $eventId does not point to an existing event
     */
    public function cancelEvent($eventId)
    {
        /** @var CancelEventCommand $cancelEventCommand */
        $cancelEventCommand = $this->container->get(CancelEventCommand::SERVICE_NAME);

        $cancelEventCommand->setEventId($eventId);

        $cancelEventCommand->run();
    }

    /**
     * Remove an event
     *
     * @param int $eventId
     */
    public function removeEvent($eventId)
    {
        /** @var RemoveEventCommand $removeEventCommand */
        $removeEventCommand = $this->container->get(RemoveEventCommand::SERVICE_NAME);

        $removeEventCommand->setEventId($eventId);

        $removeEventCommand->run();
    }

    /**
     * Create a new signup with the given values
     *
     * @param int $eventId
     * @param Account $account
     * @param string $signUpType
     * @param array $roles
     *
     * @throws EventDoesNotExistException if the event does not exist
     * @throws UserAlreadySignedException if the user is already signed
     * @throws EventInThePastException if the event is in the past
     */
    public function createSignUp($eventId, Account $account, $signUpType, $roles = [])
    {
        /** @var CreateSignUpCommand $createSignUpCommand */
        $createSignUpCommand = $this->container->get(CreateSignUpCommand::SERVICE_NAME);

        $createSignUpCommand->setEventId($eventId);
        $createSignUpCommand->setAccount($account);
        $createSignUpCommand->setSignUpType($signUpType);
        $createSignUpCommand->setRoles($roles);

        $createSignUpCommand->run();
    }

    /**
     * Update an existing sign up with the supplied new information
     *
     * @param int $signUpId
     * @param SignUpFormModel $formModel
     */
    public function updateSignUp($signUpId, SignUpFormModel $formModel)
    {
        /** @var UpdateSignUpCommand $updateSignUpCommand */
        $updateSignUpCommand = $this->container->get(UpdateSignUpCommand::SERVICE_NAME);

        $updateSignUpCommand->setSignUpId($signUpId);
        $updateSignUpCommand->setFormModel($formModel);

        $updateSignUpCommand->run();
    }

    /**
     * Remove a sign up
     *
     * @param int $signUpId
     */
    public function removeSignUp($signUpId)
    {
        /** @var RemoveSignUpCommand $removeSignUpCommand */
        $removeSignUpCommand = $this->container->get(RemoveSignUpCommand::SERVICE_NAME);

        $removeSignUpCommand->setSignUpId($signUpId);

        $removeSignUpCommand->run();
    }

    /**
     * Remove the sign up the given account has on the given event
     *
     * @param $eventId
     * @param $accountId
     */
    public function removeSignUpForAccount($eventId, $accountId)
    {
        /** @var RemoveSignUpForAccountCommand $removeSignUpForAccountCommand */
        $removeSignUpForAccountCommand = $this->container->get(RemoveSignUpForAccountCommand::SERVICE_NAME);

        $removeSignUpForAccountCommand->setEventId($eventId);
        $removeSignUpForAccountCommand->setAccountId($accountId);

        $removeSignUpForAccountCommand->run();
    }

    /**
     * If it exists, return the sign up the given user has for the given event
     *
     * @param $eventId
     * @param $accountId
     *
     * @return SignUp
     */
    public function getSignUpForUser($eventId, $accountId)
    {
        /** @var UserSignUpQuery $userSignUpQuery */
        $userSignUpQuery = $this->container->get(UserSignUpQuery::SERVICE_NAME);

        $userSignUpQuery->setEventId($eventId);
        $userSignUpQuery->setAccountId($accountId);

        return $userSignUpQuery->run();
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