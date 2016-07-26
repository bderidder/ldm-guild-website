<?php

namespace LaDanse\ServicesBundle\Service\Event\Query;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\Event;
use LaDanse\ServicesBundle\Common\AbstractQuery;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @DI\Service(GetAllEventsSinceQuery::SERVICE_NAME, public=true, shared=false)
 */
class GetAllEventsSinceQuery extends AbstractQuery
{
    const SERVICE_NAME = 'LaDanse.GetAllEventsSinceQuery';

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

    /** @var \DateTime $sinceDate */
    private $sinceDate;

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
    public function getSinceDate()
    {
        return $this->sinceDate;
    }

    /**
     * @param \DateTime $sinceDate
     *
     * @return GetAllEventsSinceQuery
     */
    public function setSinceDate($sinceDate)
    {
        $this->sinceDate = $sinceDate;

        return $this;
    }

    protected function validateInput()
    {
    }

    protected function runQuery()
    {
        $em = $this->doctrine->getManager();

        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->select('e')
            ->from('LaDanse\DomainBundle\Entity\Event', 'e')
            ->where('e.inviteTime > :start')
            ->orderBy('e.inviteTime', 'ASC')
            ->setParameter('start', $this->getSinceDate());

        $this->logger->debug(
            __CLASS__ . " created DQL for retrieving Events ",
            array(
                "query" => $qb->getDQL()
            )
        );

        /* @var $query \Doctrine\ORM\Query */
        $query = $qb->getQuery();

        $events = $query->getResult();

        return $events;
    }
}