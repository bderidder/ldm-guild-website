<?php

namespace LaDanse\ServicesBundle\Service\GameData\Query;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\ServicesBundle\Common\AbstractQuery;
use LaDanse\ServicesBundle\Service\DTO\GameData\GameClassMapper;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @DI\Service(GetAllGameClassesQuery::SERVICE_NAME, public=true, shared=false)
 */
class GetAllGameClassesQuery extends AbstractQuery
{
    const SERVICE_NAME = 'LaDanse.GetAllGameClassesQuery';

    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    public $logger;

    /**
     * @var $logger \Doctrine\Bundle\DoctrineBundle\Registry
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

    protected function validateInput()
    {
        // no input to validate for this query
    }

    protected function runQuery()
    {
        $em = $this->doctrine->getManager();

        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->select('g')
            ->from('LaDanse\DomainBundle\Entity\GameData\GameClass', 'g')
            ->orderBy('g.name', 'ASC');

        $this->logger->debug(
            __CLASS__ . " created DQL for retrieving GameClasses ",
            [
                "query" => $qb->getDQL()
            ]
        );

        /* @var $query \Doctrine\ORM\Query */
        $query = $qb->getQuery();

        $gameClasses = $query->getResult();

        return GameClassMapper::mapArray($gameClasses);
    }
}