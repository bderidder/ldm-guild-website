<?php

namespace LaDanse\ServicesBundle\Service\GameData\Query;

use Doctrine\ORM\Mapping\ClassMetadata;
use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\ServicesBundle\Common\AbstractQuery;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @DI\Service(GetAllGuildsQuery::SERVICE_NAME, public=true, shared=false)
 */
class GetAllGuildsQuery extends AbstractQuery
{
    const SERVICE_NAME = 'LaDanse.GetAllGuildsQuery';

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

        $qb->select('e')
            ->from('LaDanse\DomainBundle\Entity\GameData\Guild', 'g')
            ->orderBy('g.name', 'ASC');

        $this->logger->debug(
            __CLASS__ . " created DQL for retrieving Guilds ",
            [
                "query" => $qb->getDQL()
            ]
        );

        /* @var $query \Doctrine\ORM\Query */
        $query = $qb->getQuery();

        $query->setFetchMode('LaDanse\DomainBundle\Entity\GameData\Realm', "realm", ClassMetadata::FETCH_EAGER);

        $guilds = $query->getResult();
    }
}