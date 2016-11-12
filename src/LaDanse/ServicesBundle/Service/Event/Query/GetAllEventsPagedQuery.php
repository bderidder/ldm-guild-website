<?php

namespace LaDanse\ServicesBundle\Service\Event\Query;

use DateInterval;
use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\Event;
use LaDanse\ServicesBundle\Service\DTO as DTO;
use LaDanse\ServicesBundle\Common\AbstractQuery;
use LaDanse\ServicesBundle\Common\InvalidInputException;
use LaDanse\ServicesBundle\Service\DTO\Event\EventMapper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @DI\Service(GetAllEventsPagedQuery::SERVICE_NAME, public=true, shared=false)
 */
class GetAllEventsPagedQuery extends AbstractQuery
{
    const SERVICE_NAME = 'LaDanse.GetAllEventsPagedQuery';

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
     * @var \DateTime
     */
    private $startOn;

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
     * @return \DateTime
     */
    public function getStartOn(): \DateTime
    {
        return $this->startOn;
    }

    /**
     * @param \DateTime $startOn
     * @return GetAllEventsPagedQuery
     */
    public function setStartOn(\DateTime $startOn): GetAllEventsPagedQuery
    {
        $this->startOn = $startOn;
        return $this;
    }

    protected function validateInput()
    {
        if ($this->startOn == null)
        {
            throw new InvalidInputException("A valid date is required for startOn");
        }
    }

    protected function runQuery()
    {
        $this->startOn->setTime(0, 0, 0);

        $beforeDate = clone $this->getStartOn();
        $beforeDate->add(new DateInterval('P28D'));
        $beforeDate->setTime(23, 59, 59);

        $em = $this->doctrine->getManager();

        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->select('event')
            ->from(Event::class, 'event')
            ->where($qb->expr()->andX(
                $qb->expr()->gte('event.inviteTime', ':startOn'),
                $qb->expr()->lt('event.inviteTime', ':beforeDate')
            ))
            ->orderBy('event.inviteTime', 'ASC')
            ->setParameter('startOn', $this->getStartOn())
            ->setParameter('beforeDate', $beforeDate);

        $this->logger->debug(
            __CLASS__ . " created DQL for retrieving Events ",
            [
                "query" => $qb->getDQL()
            ]
        );

        /* @var $query \Doctrine\ORM\Query */
        $query = $qb->getQuery();

        $events = $query->getResult();

        /** @var EventHydrator $eventHydrator */
        $eventHydrator = $this->container->get(EventHydrator::SERVICE_NAME);

        $eventIds = [];

        foreach($events as $event)
        {
            /** @var Event $event */
            $eventIds[] = $event->getId();
        }

        $eventHydrator->setEventIds($eventIds);

        $previousTimestamp = clone $this->getStartOn();
        $previousTimestamp->sub(new DateInterval('P28D'));

        $nextTimestamp = clone $this->getStartOn();
        $nextTimestamp->add(new DateInterval('P28D'));

        $eventPage = new DTO\Event\EventPage();
        $eventPage
            ->setEvents(EventMapper::mapArray($events, $eventHydrator))
            ->setPreviousTimestamp($previousTimestamp)
            ->setNextTimestamp($nextTimestamp);

        return $eventPage;
    }
}