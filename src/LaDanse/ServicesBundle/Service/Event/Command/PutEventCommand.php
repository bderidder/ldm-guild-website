<?php

namespace LaDanse\ServicesBundle\Service\Event\Command;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\Event;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Common\AbstractCommand;
use LaDanse\ServicesBundle\Common\InvalidInputException;
use LaDanse\ServicesBundle\Service\Authorization\AuthorizationService;
use LaDanse\ServicesBundle\Service\Authorization\ResourceByValue;
use LaDanse\ServicesBundle\Service\Authorization\SubjectReference;
use LaDanse\ServicesBundle\Service\Event\EventService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use LaDanse\DomainBundle\Entity as Entity;
use LaDanse\ServicesBundle\Service\DTO as DTO;

/**
 * @DI\Service(PutEventCommand::SERVICE_NAME, public=true, shared=false)
 */
class PutEventCommand extends AbstractCommand
{
    const SERVICE_NAME = 'LaDanse.PutEventCommand';

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
     * @var int
     */
    private $eventId;

    /**
     * @var DTO\Event\PutEvent
     */
    private $putEventDto;

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
     * @return PutEventCommand
     */
    public function setEventId(int $eventId): PutEventCommand
    {
        $this->eventId = $eventId;
        return $this;
    }

    /**
     * @return DTO\Event\PutEvent
     */
    public function getPutEventDto(): DTO\Event\PutEvent
    {
        return $this->putEventDto;
    }

    /**
     * @param DTO\Event\PutEvent $putEventDto
     * @return PutEventCommand
     */
    public function setPutEventDto(DTO\Event\PutEvent $putEventDto): PutEventCommand
    {
        $this->putEventDto = $putEventDto;
        return $this;
    }

    protected function validateInput()
    {
        $inviteTime = $this->getPutEventDto()->getInviteTime();
        $startTime = $this->getPutEventDto()->getStartTime();
        $endTime = $this->getPutEventDto()->getEndTime();

        if (!(($inviteTime <= $startTime) && ($startTime <= $endTime)))
        {
            throw new InvalidInputException("Violation of time constraints: inviteTime <= startTime <= endTime");
        }
    }

    protected function runCommand()
    {
        /** @var EventService $eventService */
        $eventService = $this->container->get(EventService::SERVICE_NAME);

        $em = $this->doctrine->getManager();

        /** @var Entity\Event $event */
        $event = $em->getRepository(Event::REPOSITORY)->find($this->getEventId());

        $oldEventDto = $eventService->getEventById($event->getId());

        $this->authzService->allowOrThrow(
            new SubjectReference($this->getAccount()),
            ActivityType::EVENT_EDIT,
            new ResourceByValue(DTO\Event\Event::class, $oldEventDto)
        );

        $event->setOrganiser(
            $em->getReference(
                Entity\Account::class,
                $this->getPutEventDto()->getOrganiserReference()->getId()
            )
        );
        $event->setName($this->getPutEventDto()->getName());
        $event->setDescription($this->getPutEventDto()->getDescription());
        $event->setInviteTime($this->getPutEventDto()->getInviteTime());
        $event->setStartTime($this->getPutEventDto()->getStartTime());
        $event->setEndTime($this->getPutEventDto()->getEndTime());

        $this->logger->info(__CLASS__ . ' updating event');

        $em->flush();

        $newEventDto = $eventService->getEventById($event->getId());

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::EVENT_EDIT,
                $this->getAccount(),
                [
                    'oldEvent' => ActivityEvent::annotatedToSimpleObject($oldEventDto),
                    'newEvent' => ActivityEvent::annotatedToSimpleObject($newEventDto)
                ]
            )
        );

        return $newEventDto;
    }
}