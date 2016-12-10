<?php

namespace LaDanse\ServicesBundle\Service\Event\Query;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\Event;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Common\AbstractQuery;
use LaDanse\ServicesBundle\Service\Authorization\AuthorizationService;
use LaDanse\ServicesBundle\Service\Authorization\ResourceByValue;
use LaDanse\ServicesBundle\Service\Authorization\SubjectReference;
use LaDanse\ServicesBundle\Service\DTO\Event\EventMapper;
use LaDanse\ServicesBundle\Service\Event\EventDoesNotExistException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @DI\Service(GetEventByIdQuery::SERVICE_NAME, public=true, shared=false)
 */
class GetEventByIdQuery extends AbstractQuery
{
    const SERVICE_NAME = 'LaDanse.GetEventByIdQuery';

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

    protected function runQuery()
    {
        /* @var $repository \Doctrine\ORM\EntityRepository */
        $repository = $this->doctrine->getRepository(Event::REPOSITORY);

        /* @var $event \LaDanse\DomainBundle\Entity\Event */
        $event = $repository->find($this->getEventId());

        if (is_null($event))
        {
            throw new EventDoesNotExistException('Event does not exist');
        }

        $this->authzService->allowOrThrow(
            new SubjectReference($this->getAccount()),
            ActivityType::EVENT_VIEW,
            new ResourceByValue(Event::class, $event)
        );

        /** @var EventHydrator $eventHydrator */
        $eventHydrator = $this->container->get(EventHydrator::SERVICE_NAME);

        $eventIds = [$event->getId()];

        $eventHydrator->setEventIds($eventIds);

        return EventMapper::mapSingle($event, $eventHydrator);
    }
}