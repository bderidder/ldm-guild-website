<?php

namespace LaDanse\ServicesBundle\Service\Event\Command;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity as Entity;
use LaDanse\DomainBundle\Entity\Event;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Common\AbstractCommand;
use LaDanse\ServicesBundle\Common\InvalidInputException;
use LaDanse\ServicesBundle\Service\Authorization\AuthorizationService;
use LaDanse\ServicesBundle\Service\Authorization\ResourceByValue;
use LaDanse\ServicesBundle\Service\Authorization\SubjectReference;
use LaDanse\ServicesBundle\Service\Comments\CommentService;
use LaDanse\ServicesBundle\Service\DTO as DTO;
use LaDanse\ServicesBundle\Service\Event\EventService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @DI\Service(PostEventCommand::SERVICE_NAME, public=true, shared=false)
 */
class PostEventCommand extends AbstractCommand
{
    const SERVICE_NAME = 'LaDanse.PostEventCommand';

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
     * @var DTO\Event\PostEvent
     */
    private $postEventDto;

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
     * @return DTO\Event\PostEvent
     */
    public function getPostEventDto(): DTO\Event\PostEvent
    {
        return $this->postEventDto;
    }

    /**
     * @param DTO\Event\PostEvent $postEventDto
     * @return PostEventCommand
     */
    public function setPostEventDto(DTO\Event\PostEvent $postEventDto): PostEventCommand
    {
        $this->postEventDto = $postEventDto;
        return $this;
    }

    protected function validateInput()
    {
        $inviteTime = $this->getPostEventDto()->getInviteTime();
        $startTime = $this->getPostEventDto()->getStartTime();
        $endTime = $this->getPostEventDto()->getEndTime();

        if (!(($inviteTime <= $startTime) && ($startTime <= $endTime)))
        {
            throw new InvalidInputException("Violation of time constraints: inviteTime <= startTime <= endTime");
        }
    }

    protected function runCommand()
    {
        $this->authzService->allowOrThrow(
            new SubjectReference($this->getAccount()),
            ActivityType::EVENT_CREATE,
            new ResourceByValue(DTO\Event\PostEvent::class, $this->getPostEventDto())
        );

        $em = $this->doctrine->getManager();

        /** @var $commentService CommentService */
        $commentService = $this->container->get(CommentService::SERVICE_NAME);

        $commentGroupId = $commentService->createCommentGroup();

        $event = new Event();
        $event->setOrganiser(
            $em->getReference(
                Entity\Account::class,
                $this->getPostEventDto()->getOrganiserReference()->getId()
            )
        );
        $event->setName($this->getPostEventDto()->getName());
        $event->setDescription($this->getPostEventDto()->getDescription());
        $event->setInviteTime($this->getPostEventDto()->getInviteTime());
        $event->setStartTime($this->getPostEventDto()->getStartTime());
        $event->setEndTime($this->getPostEventDto()->getEndTime());
        $event->setTopicId($commentGroupId);

        $this->logger->info(__CLASS__ . ' persisting event');

        $em->persist($event);
        $em->flush();

        /** @var EventService $eventService */
        $eventService = $this->container->get(EventService::SERVICE_NAME);
        $eventDto = $eventService->getEventById($event->getId());

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::EVENT_CREATE,
                $this->getAccount(),
                [
                    'event' => ActivityEvent::annotatedToSimpleObject($eventDto)
                ]
            )
        );

        return $eventDto;
    }
}