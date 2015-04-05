<?php

namespace LaDanse\ServicesBundle\Service\Event;

use LaDanse\CommonBundle\Helper\LaDanseService;
use LaDanse\DomainBundle\Entity\Account;
use LaDanse\ServicesBundle\Service\Event\Command\CreateEventCommand;
use LaDanse\ServicesBundle\Service\Event\Command\CreateSignUpCommand;
use LaDanse\ServicesBundle\Service\Event\Command\EventDoesNotExistException;
use LaDanse\ServicesBundle\Service\Event\Command\EventInThePastException;
use LaDanse\ServicesBundle\Service\Event\Command\UserAlreadySignedException;
use Symfony\Component\DependencyInjection\ContainerInterface;

use \Doctrine\Bundle\DoctrineBundle\Registry;

use LaDanse\DomainBundle\Entity\Event;

use JMS\DiExtraBundle\Annotation as DI;

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
     * Return the event with the given id
     *
     * @param $id int id of event to retrieve
     *
     * @throws EventDoesNotExistException if the event does not exist
     *
     * @return Event
     */
    public function getEventById($id)
    {
        /* @var $repository \Doctrine\ORM\EntityRepository */
        $repository = $this->doctrine->getRepository(Event::REPOSITORY);

        /* @var $event \LaDanse\DomainBundle\Entity\Event */
        $event = $repository->find($id);

        if (is_null($event))
        {
            throw new EventDoesNotExistException('Event does not exist');
        }

        return $event;
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
        /** @var $createEventCommand CreateEventCommand */
        $createEventCommand = $this->container->get(CreateEventCommand::SERVICE_NAME);

        $createEventCommand->setOrganiser($organiser);
        $createEventCommand->setName($name);
        $createEventCommand->setDescription($description);
        $createEventCommand->setInviteTime($inviteTime);
        $createEventCommand->setStartTime($startTime);
        $createEventCommand->setEndTime($endTIme);

        $createEventCommand->run();
    }

    public function updateEvent()
    {

    }

    public function removeEvent()
    {

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
    public function createSignUp($eventId, Account $account, $signUpType, $roles = array())
    {
        /** @var $createSignUpCommand CreateSignUpCommand */
        $createSignUpCommand = $this->container->get(CreateSignUpCommand::SERVICE_NAME);

        $createSignUpCommand->setEventId($eventId);
        $createSignUpCommand->setAccount($account);
        $createSignUpCommand->setSignUpType($signUpType);
        $createSignUpCommand->setRoles($roles);

        $createSignUpCommand->run();
    }

    public function updateSignUp()
    {

    }

    public function removeSignUp()
    {

    }
}