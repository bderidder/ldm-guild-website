<?php

namespace LaDanse\ServicesBundle\Service\Event\Command;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\Event;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Common\AbstractCommand;
use LaDanse\ServicesBundle\Service\Event\EventDoesNotExistException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @DI\Service(UpdateEventCommand::SERVICE_NAME, public=true, shared=false)
 */
class UpdateEventCommand extends AbstractCommand
{
    const SERVICE_NAME = 'LaDanse.UpdateEventCommand';

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
    /** @var string $name */
    private $name;
    /** @var string $description */
    private $description;
    /** @var \DateTime $inviteTime */
    private $inviteTime;
    /** @var \DateTime $startTime */
    private $startTime;
    /** @var \DateTime $endTime */
    private $endTime;

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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return \DateTime
     */
    public function getInviteTime()
    {
        return $this->inviteTime;
    }

    /**
     * @param \DateTime $inviteTime
     */
    public function setInviteTime($inviteTime)
    {
        $this->inviteTime = $inviteTime;
    }

    /**
     * @return \DateTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @param \DateTime $startTime
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    }

    /**
     * @return \DateTime
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @param \DateTime $endTime
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;
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

        $oldJson = $event->toJson();

        $event->setName($this->getName());
        $event->setDescription($this->getDescription());
        $event->setInviteTime($this->getInviteTime());
        $event->setStartTime($this->getStartTime());
        $event->setEndTime($this->getEndTime());

        $this->logger->info(__CLASS__ . ' updating event');

        $em->flush();

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::EVENT_EDIT,
                $this->getAccount(),
                array(
                    'oldEvent' => $oldJson,
                    'newEvent' => $event->toJson()
                )
            )
        );
    }
}