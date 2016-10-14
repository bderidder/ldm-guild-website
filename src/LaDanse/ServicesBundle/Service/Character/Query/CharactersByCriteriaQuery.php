<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Service\Character\Query;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\InGuild;
use LaDanse\ServicesBundle\Common\AbstractQuery;
use LaDanse\ServicesBundle\Common\InvalidInputException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use LaDanse\DomainBundle\Entity as Entity;
use LaDanse\ServicesBundle\Service\DTO as DTO;

/**
 * @DI\Service(CharactersByCriteriaQuery::SERVICE_NAME, public=true, shared=false)
 */
class CharactersByCriteriaQuery extends AbstractQuery
{
    const SERVICE_NAME = 'LaDanse.CharactersByCriteriaQuery';

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

    /** @var DTO\Character\SearchCriteria $searchCriteria */
    private $searchCriteria;

    /** @var $onDateTime \DateTime */
    private $onDateTime;

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
     * @return DTO\Character\SearchCriteria
     */
    public function getSearchCriteria(): DTO\Character\SearchCriteria
    {
        return $this->searchCriteria;
    }

    /**
     * @param DTO\Character\SearchCriteria $searchCriteria
     * @return CharactersByCriteriaQuery
     */
    public function setSearchCriteria(DTO\Character\SearchCriteria $searchCriteria): CharactersByCriteriaQuery
    {
        $this->searchCriteria = $searchCriteria;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getOnDateTime(): \DateTime
    {
        return $this->onDateTime;
    }

    /**
     * @param \DateTime $onDateTime
     * @return CharactersByCriteriaQuery
     */
    public function setOnDateTime(\DateTime $onDateTime): CharactersByCriteriaQuery
    {
        $this->onDateTime = $onDateTime;
        return $this;
    }

    protected function validateInput()
    {
        if ($this->onDateTime == null)
        {
            throw new InvalidInputException(
                "Input for " . __CLASS__ . " is not valid",
                400
            );
        }
    }

    protected function runQuery()
    {
        $em = $this->doctrine->getManager();

        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $whereClause = $qb->expr()->andX(
            $qb->expr()->andX(
                $qb->expr()->gte('characterVersion.level', ':minLevel'),
                $qb->expr()->lte('characterVersion.level', ':maxLevel')
            ),
            $qb->expr()->andX(
                $qb->expr()->like('character.name', ':name'),
                $qb->expr()->orX(
                    $qb->expr()->andX(
                        $qb->expr()->lte('characterVersion.fromTime', ':onDateTime'),
                        $qb->expr()->gt('characterVersion.endTime', ':onDateTime')
                    ),
                    $qb->expr()->andX(
                        $qb->expr()->lte('characterVersion.fromTime', ':onDateTime'),
                        $qb->expr()->isNull('characterVersion.endTime')
                    )
                )
            )
        );

        $guildParamRequired = false;
        $raceParamRequired = false;
        $classParamRequired = false;
        $factionParamRequired = false;
        $rolesParamRequired = false;

        // if Race is selected, add clause
        if ($this->getSearchCriteria()->getGameRace() !== null)
        {
            $whereClause = $qb->expr()->andX(
                $whereClause,
                $qb->expr()->eq('gameRace.id', ':gameRaceId')
            );
            $raceParamRequired = true;
        }

        // if Class is selected, add clause
        if ($this->getSearchCriteria()->getGameClass() !== null)
        {
            $whereClause = $qb->expr()->andX(
                $whereClause,
                $qb->expr()->eq('gameClass.id', ':gameClassId')
            );
            $classParamRequired = true;
        }

        // if Faction is selected, add clause
        if ($this->getSearchCriteria()->getGameFaction() !== null)
        {
            $whereClause = $qb->expr()->andX(
                $whereClause,
                $qb->expr()->eq('gameFaction.id', ':gameFactionId')
            );
            $factionParamRequired = true;
        }

        // if Guild is selected, add clause (this is a sub-query)
        if ($this->getSearchCriteria()->getGuild() != null)
        {
            /** @var \Doctrine\ORM\QueryBuilder $innerGuildQb */
            $innerGuildQb = $em->createQueryBuilder();

            $whereClause = $qb->expr()->andX(
                $whereClause,
                $qb->expr()->in(
                    'characterVersion.character',
                    $innerGuildQb->select('innerCharacter.id')
                        ->from(Entity\InGuild::class, 'inGuild')
                        ->join('inGuild.character', 'innerCharacter')
                        ->join('inGuild.guild', 'innerGuild')
                        ->add('where',
                            $qb->expr()->andX(
                                $qb->expr()->eq('innerGuild.id', ':guildId'),
                                $qb->expr()->orX(
                                    $qb->expr()->andX(
                                        $qb->expr()->lte('inGuild.fromTime', ':onDateTime'),
                                        $qb->expr()->gt('inGuild.endTime', ':onDateTime')
                                    ),
                                    $qb->expr()->andX(
                                        $qb->expr()->lte('inGuild.fromTime', ':onDateTime'),
                                        $qb->expr()->isNull('inGuild.endTime')
                                    )
                                )
                            )
                        )->getDQL()
                )
            );
            $guildParamRequired = true;
        }

        // if any of "only raider" or "only non-raider" is selected, add clause (this is a sub-query)
        if (($this->getSearchCriteria()->getRaider() == 2) || ($this->getSearchCriteria()->getRaider() == 3))
        {
            $raiderValue = $this->getSearchCriteria()->getRaider() == 2 ? 1 : 0;

            /** @var \Doctrine\ORM\QueryBuilder $innerRaiderdQb */
            $innerRaiderdQb = $em->createQueryBuilder();

            $whereClause = $qb->expr()->andX(
                $whereClause,
                $qb->expr()->in(
                    'characterVersion.character',
                    $innerRaiderdQb->select('innerRaiderCharacter.id')
                        ->from(Entity\Claim::class, 'raiderClaim')
                        ->join('raiderClaim.character', 'innerRaiderCharacter')
                        ->add('where',
                            $qb->expr()->andX(
                                $qb->expr()->eq('raiderClaim.raider', $raiderValue),
                                $qb->expr()->orX(
                                    $qb->expr()->andX(
                                        $qb->expr()->lte('raiderClaim.fromTime', ':onDateTime'),
                                        $qb->expr()->gt('raiderClaim.endTime', ':onDateTime')
                                    ),
                                    $qb->expr()->andX(
                                        $qb->expr()->lte('raiderClaim.fromTime', ':onDateTime'),
                                        $qb->expr()->isNull('raiderClaim.endTime')
                                    )
                                )
                            )
                        )->getDQL()
                )
            );
        }

        // if "only claimed" is selected, add clause (this is a sub-query)
        if (($this->getSearchCriteria()->getClaimed() == 2) && ($this->getSearchCriteria()->getRaider() == 1))
        {
            /** @var \Doctrine\ORM\QueryBuilder $innerRaiderdQb */
            $innerRaiderdQb = $em->createQueryBuilder();

            $whereClause = $qb->expr()->andX(
                $whereClause,
                $qb->expr()->in(
                    'characterVersion.character',
                    $innerRaiderdQb->select('innerClaimCharacter.id')
                        ->from(Entity\Claim::class, 'claim')
                        ->join('claim.character', 'innerClaimCharacter')
                        ->add('where',
                            $qb->expr()->orX(
                                $qb->expr()->andX(
                                    $qb->expr()->lte('claim.fromTime', ':onDateTime'),
                                    $qb->expr()->gt('claim.endTime', ':onDateTime')
                                ),
                                $qb->expr()->andX(
                                    $qb->expr()->lte('claim.fromTime', ':onDateTime'),
                                    $qb->expr()->isNull('claim.endTime')
                                )
                            )
                        )->getDQL()
                )
            );
        }

        // if "only non-claimed" is selected, add clause (this is a sub-query)
        if (($this->getSearchCriteria()->getClaimed() == 3) && ($this->getSearchCriteria()->getRaider() == 1))
        {
            /** @var \Doctrine\ORM\QueryBuilder $innerRaiderdQb */
            $innerRaiderdQb = $em->createQueryBuilder();

            $whereClause = $qb->expr()->andX(
                $whereClause,
                $qb->expr()->notIn(
                    'characterVersion.character',
                    $innerRaiderdQb->select('innerClaimCharacter.id')
                        ->from(Entity\Claim::class, 'claim')
                        ->join('claim.character', 'innerClaimCharacter')
                        ->add('where',
                            $qb->expr()->orX(
                                $qb->expr()->andX(
                                    $qb->expr()->lte('claim.fromTime', ':onDateTime'),
                                    $qb->expr()->gt('claim.endTime', ':onDateTime')
                                ),
                                $qb->expr()->andX(
                                    $qb->expr()->lte('claim.fromTime', ':onDateTime'),
                                    $qb->expr()->isNull('claim.endTime')
                                )
                            )
                        )->getDQL()
                )
            );
        }

        // if "only non-claimed" is selected, add clause (this is a sub-query)
        if (($this->getSearchCriteria()->getRoles() != null) && (count($this->getSearchCriteria()->getRoles()) > 0))
        {
            /** @var \Doctrine\ORM\QueryBuilder $innerRolesQb */
            $innerRolesQb = $em->createQueryBuilder();

            $whereClause = $qb->expr()->andX(
                $whereClause,
                $qb->expr()->in(
                    'characterVersion.character',
                    $innerRolesQb->select('innerRolesCharacter.id')
                        ->from(Entity\PlaysRole::class, 'playsRole')
                        ->join('playsRole.claim', 'innerRolesClaim')
                        ->join('innerRolesClaim.character', 'innerRolesCharacter')
                        ->add('where',
                            $innerRolesQb->expr()->andX(
                                $innerRolesQb->expr()->in('playsRole.role', ':roles'),
                                $innerRolesQb->expr()->orX(
                                    $innerRolesQb->expr()->andX(
                                        $innerRolesQb->expr()->lte('playsRole.fromTime', ':onDateTime'),
                                        $innerRolesQb->expr()->gt('playsRole.endTime', ':onDateTime')
                                    ),
                                    $innerRolesQb->expr()->andX(
                                        $innerRolesQb->expr()->lte('playsRole.fromTime', ':onDateTime'),
                                        $innerRolesQb->expr()->isNull('playsRole.endTime')
                                    )
                                )
                            )
                        )->getDQL()
                )
            );

            $rolesParamRequired = true;
        }

        $qb->select('characterVersion', 'character', 'realm', 'gameClass', 'gameRace')
           ->from(Entity\CharacterVersion::class, 'characterVersion')
           ->join('characterVersion.character', 'character')
           ->join('characterVersion.gameClass', 'gameClass')
           ->join('characterVersion.gameRace', 'gameRace')
           ->join('gameRace.faction', 'gameFaction')
           ->join('character.realm', 'realm')
           ->add('where', $whereClause)
           ->setMaxResults(50);

        $qb->setParameter('minLevel', $this->getSearchCriteria()->getMinLevel())
           ->setParameter('maxLevel', $this->getSearchCriteria()->getMaxLevel())
           ->setParameter('onDateTime', $this->getOnDateTime())
           ->setParameter('name', '%' . $this->getSearchCriteria()->getName() . '%');

        if ($guildParamRequired)
            $qb->setParameter('guildId', $this->getSearchCriteria()->getGuild());

        if ($raceParamRequired)
            $qb->setParameter('gameRaceId', $this->getSearchCriteria()->getGameRace());

        if ($classParamRequired)
            $qb->setParameter('gameClassId', $this->getSearchCriteria()->getGameClass());

        if ($factionParamRequired)
            $qb->setParameter('gameFactionId', $this->getSearchCriteria()->getGameFaction());

        if ($rolesParamRequired)
            $qb->setParameter('roles', $this->getSearchCriteria()->getRoles());

        /* @var $query \Doctrine\ORM\Query */
        $query = $qb->getQuery();

        $characterVersions = $query->getResult();

        $characterIds = [];

        foreach($characterVersions as $characterVersion)
        {
            /** @var Entity\CharacterVersion $characterVersion */

            $characterIds[] = $characterVersion->getCharacter()->getId();
        }

        /** @var CharacterHydrator $characterHydrator */
        $characterHydrator = $this->container->get(CharacterHydrator::SERVICE_NAME);
        $characterHydrator->setCharacterIds($characterIds);
        $characterHydrator->setOnDateTime($this->getOnDateTime());

        return DTO\Character\CharacterMapper::mapArray($characterVersions, $characterHydrator);
    }
}