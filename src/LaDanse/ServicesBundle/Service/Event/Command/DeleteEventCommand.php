<?php

namespace LaDanse\ServicesBundle\Service\Event\Command;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\Event;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Common\AbstractCommand;
use LaDanse\ServicesBundle\Service\Authorization\AuthorizationService;
use LaDanse\ServicesBundle\Service\Authorization\ResourceByValue;
use LaDanse\ServicesBundle\Service\Authorization\SubjectReference;
use LaDanse\ServicesBundle\Service\Comments\CommentService;
use LaDanse\ServicesBundle\Service\Event\EventDoesNotExistException;
use LaDanse\ServicesBundle\Service\Event\EventInThePastException;
use LaDanse\ServicesBundle\Service\Event\EventService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use LaDanse\ServicesBundle\Service\DTO as DTO;

/**
 * @DI\Service(DeleteEventCommand::SERVICE_NAME, public=true, shared=false)
 */
class DeleteEventCommand extends AbstractCommand
{
    const SERVICE_NAME = 'LaDanse.DeleteEventCommand';

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
        /** @var EventService $eventService */
        $eventService = $this->container->get(EventService::SERVICE_NAME);

        /** @var $commentService CommentService */
        $commentService = $this->container->get(CommentService::SERVICE_NAME);

        $eventDto = $eventService->getEventById($this->getEventId());

        if ($eventDto == null)
        {
            throw new EventDoesNotExistException("Event does not exist, id = " . $this->getEventId());
        }

        $this->authzService->allowOrThrow(
            new SubjectReference($this->getAccount()),
            ActivityType::EVENT_DELETE,
            new ResourceByValue(DTO\Event\Event::class, $eventDto)
        );

        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->doctrine->getManager();

        /* @var $repository \Doctrine\ORM\EntityRepository */
        $repository = $this->doctrine->getRepository(Event::REPOSITORY);

        /* @var $repository \Doctrine\ORM\EntityRepository */
        $event = $repository->find($this->getEventId());

        $currentDateTime = new \DateTime();
        if ($event->getInviteTime() <= $currentDateTime)
        {
            throw new EventInThePastException("Event is in the past and cannot be changed");
        }

        $commentService->removeCommentGroup($event->getTopicId());

        $em->remove($event);

        $em->flush();

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::EVENT_DELETE,
                $this->getAccount(),
                [
                    'event' => ActivityEvent::annotatedToSimpleObject($eventDto)
                ]
            )
        );
    }
}