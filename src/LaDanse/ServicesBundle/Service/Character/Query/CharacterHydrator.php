<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Service\Character\Query;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity as Entity;

/**
 * @DI\Service(CharacterHydrator::SERVICE_NAME, public=true, shared=false)
 */
class CharacterHydrator
{
    const SERVICE_NAME = 'LaDanse.CharacterHydrator';

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

    /** @var array $characterId */
    private $characterIds;

    /** @var $onDateTime \DateTime */
    private $onDateTime;

    /** @var bool $initialized */
    private $initialized = false;

    /** @var array $claimVersions */
    private $claimVersions;

    /** @var array $playsRoles */
    private $playsRoles;

    /** @var array $inGuilds */
    private $inGuilds;

    /**
     * @return array
     */
    public function getCharacterIds(): array
    {
        return $this->characterIds;
    }

    /**
     * @param array $characterIds
     * @return CharacterHydrator
     */
    public function setCharacterIds(array $characterIds): CharacterHydrator
    {
        $this->characterIds = $characterIds;
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
     * @return CharacterHydrator
     */
    public function setOnDateTime(\DateTime $onDateTime): CharacterHydrator
    {
        $this->onDateTime = $onDateTime;
        return $this;
    }

    public function hasBeenClaimed(int $characterId) : bool
    {
        $this->init();

        if ($this->claimVersions == null)
        {
            return false;
        }

        foreach($this->claimVersions as $claimVersion)
        {
            /** @var Entity\ClaimVersion $claimVersion */
            if ($claimVersion->getClaim()->getCharacter()->getId() == $characterId)
            {
                return true;
            }
        }

        return false;
    }

    /**
     * @param int $characterId
     *
     * @return Entity\ClaimVersion
     */
    public function getClaimVersion(int $characterId)
    {
        $this->init();

        if ($this->claimVersions == null)
        {
            return null;
        }

        foreach($this->claimVersions as $claimVersion)
        {
            /** @var Entity\ClaimVersion $claimVersion */
            if ($claimVersion->getClaim()->getCharacter()->getId() == $characterId)
            {
                return $claimVersion;
            }
        }

        return null;
    }

    public function getClaimedRoles(int $characterId) : array
    {
        $this->init();

        if ($this->playsRoles == null)
        {
            return [];
        }

        $roles = [];

        foreach($this->playsRoles as $playsRole)
        {
            /** @var Entity\PlaysRole $playsRole */
            if ($playsRole->getClaim()->getCharacter()->getId() == $characterId)
            {
                $roles[] = $playsRole->getRole();
            }
        }

        return $roles;
    }

    public function getGuild(int $characterId)
    {
        $this->init();

        if ($this->inGuilds == null)
        {
            return null;
        }

        $inGuild = null;

        foreach($this->inGuilds as $inGuild)
        {
            /** @var Entity\InGuild $inGuild */
            if ($inGuild->getCharacter()->getId() == $characterId)
            {
                return $inGuild;
            }
        }

        return null;
    }

    private function init()
    {
        if ($this->initialized)
            return;

        if ($this->getCharacterIds() == null || count($this->getCharacterIds()) == 0)
        {
            $this->claimVersions = [];
            $this->playsRoles = [];
            $this->inGuilds = [];
            $this->initialized = true;

            return;
        }

        $em = $this->doctrine->getManager();

        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->select('claimVersion', 'claim', 'account', 'character')
            ->from(Entity\ClaimVersion::class, 'claimVersion')
            ->join('claimVersion.claim', 'claim')
            ->join('claim.account', 'account')
            ->join('claim.character', 'character')
            ->add('where',
                $qb->expr()->andX(
                    $qb->expr()->in(
                        'character.id',
                        $this->getCharacterIds()
                    ),
                    $qb->expr()->orX(
                        $qb->expr()->andX(
                            $qb->expr()->lte('claimVersion.fromTime', '?1'),
                            $qb->expr()->gt('claimVersion.endTime', '?1')
                        ),
                        $qb->expr()->andX(
                            $qb->expr()->lte('claimVersion.fromTime', '?1'),
                            $qb->expr()->isNull('claimVersion.endTime')
                        )
                    )
                )
            )
            ->setParameter(1, $this->getOnDateTime());

        /* @var $query \Doctrine\ORM\Query */
        $query = $qb->getQuery();

        $this->claimVersions = $query->getResult();

        $claimIds = [];

        foreach($this->claimVersions as $claimVersion)
        {
            /** @var Entity\ClaimVersion $claimVersion */

            $claimIds[] = $claimVersion->getClaim()->getId();
        }

        if (count($claimIds) == 0)
        {
            $this->playsRoles = [];
        }
        else
        {
            /** @var \Doctrine\ORM\QueryBuilder $qb */
            $qb = $em->createQueryBuilder();

            $qb->select('playsRole', 'claim')
                ->from(Entity\PlaysRole::class, 'playsRole')
                ->join('playsRole.claim', 'claim')
                ->add('where',
                    $qb->expr()->andX(
                        $qb->expr()->in(
                            'claim.id',
                            $claimIds
                        ),
                        $qb->expr()->orX(
                            $qb->expr()->andX(
                                $qb->expr()->lte('playsRole.fromTime', '?1'),
                                $qb->expr()->gt('playsRole.endTime', '?1')
                            ),
                            $qb->expr()->andX(
                                $qb->expr()->lte('playsRole.fromTime', '?1'),
                                $qb->expr()->isNull('playsRole.endTime')
                            )
                        )
                    )
                )
                ->setParameter(1, $this->getOnDateTime());

            /* @var $query \Doctrine\ORM\Query */
            $query = $qb->getQuery();

            $this->playsRoles = $query->getResult();
        }

        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->select('inGuild', 'guild')
            ->from(Entity\InGuild::class, 'inGuild')
            ->join('inGuild.guild', 'guild')
            ->join('inGuild.character', 'character')
            ->add('where',
                $qb->expr()->andX(
                    $qb->expr()->in(
                        'character.id',
                        $this->getCharacterIds()
                    ),
                    $qb->expr()->orX(
                        $qb->expr()->andX(
                            $qb->expr()->lte('inGuild.fromTime', '?1'),
                            $qb->expr()->gt('inGuild.endTime', '?1')
                        ),
                        $qb->expr()->andX(
                            $qb->expr()->lte('inGuild.fromTime', '?1'),
                            $qb->expr()->isNull('inGuild.endTime')
                        )
                    )
                )
            )
            ->setParameter(1, $this->getOnDateTime());

        /* @var $query \Doctrine\ORM\Query */
        $query = $qb->getQuery();

        $this->inGuilds = $query->getResult();

        $this->initialized = true;
    }
}