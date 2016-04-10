<?php

namespace LaDanse\ServicesBundle\Service\Event\Command;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\CommentBundle\Service\CommentService;
use LaDanse\ServicesBundle\Common\AbstractCommand;
use LaDanse\DomainBundle\Entity\Account;
use LaDanse\DomainBundle\Entity\Event;

use LaDanse\ServicesBundle\Activity\ActivityEvent;


use LaDanse\ServicesBundle\Activity\ActivityType;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @DI\Service(CreateEventCommand::SERVICE_NAME, public=true, scope="prototype")
 */
class CreateEventCommand extends AbstractCommand
{
    const SERVICE_NAME = 'LaDanse.CreateEventCommand';

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

    /** @var $organiser Account */
    private $organiser;
    /** @var $name string */
    private $name;
    /** @var $description string */
    private $description;
    /** @var $inviteTime \DateTime */
    private $inviteTime;
    /** @var $startTime \DateTime */
    private $startTime;
    /** @var $endTime \DateTime */
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
     * @return Account
     */
    public function getOrganiser()
    {
        return $this->organiser;
    }

    /**
     * @param Account $organiser
     */
    public function setOrganiser($organiser)
    {
        $this->organiser = $organiser;
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
        /** @var $commentService CommentService */
        $commentService = $this->container->get(CommentService::SERVICE_NAME);

        $commentGroupId = $commentService->createCommentGroup();

        $event = new Event();
        $event->setOrganiser($this->getOrganiser());
        $event->setName($this->getName());
        $event->setDescription($this->getDescription());
        $event->setInviteTime($this->getInviteTime());
        $event->setStartTime($this->getStartTime());
        $event->setEndTime($this->getEndTime());
        $event->setTopicId($commentGroupId);

        $this->logger->info(__CLASS__ . ' persisting event');

        $em = $this->doctrine->getManager();
        $em->persist($event);
        $em->flush();

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::EVENT_CREATE,
                $this->getAccount(),
                array(
                    'event' => $event->toJson()
                )
            )
        );
    }
}