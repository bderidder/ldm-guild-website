<?php

namespace LaDanse\ServicesBundle\Service\GameData\Query;

use Doctrine\ORM\Mapping\ClassMetadata;
use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\GameData\GameFaction;
use LaDanse\ServicesBundle\Common\AbstractQuery;
use LaDanse\ServicesBundle\Service\DTO\GameData\GameRaceMapper;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @DI\Service(GetAllGameRacesQuery::SERVICE_NAME, public=true, shared=false)
 */
class GetAllGameRacesQuery extends AbstractQuery
{
    const SERVICE_NAME = 'LaDanse.GetAllGameRacesQuery';

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

        $qb->select('g', 'faction')
            ->from('LaDanse\DomainBundle\Entity\GameData\GameRace', 'g')
            ->join('g.faction', 'faction')
            ->orderBy('g.name', 'ASC');

        $this->logger->debug(
            __CLASS__ . " created DQL for retrieving GameRaces ",
            [
                "query" => $qb->getDQL()
            ]
        );

        /* @var $query \Doctrine\ORM\Query */
        $query = $qb->getQuery();

        $gameRaces = $query->getResult();

        return GameRaceMapper::mapArray($gameRaces);
    }
}