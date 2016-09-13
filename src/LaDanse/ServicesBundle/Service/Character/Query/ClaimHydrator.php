<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Service\Character\Query;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\InGuild;
use LaDanse\ServicesBundle\Common\AbstractQuery;
use LaDanse\ServicesBundle\Common\ServiceException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use LaDanse\DomainBundle\Entity as Entity;
use LaDanse\ServicesBundle\Service\DTO as DTO;

/**
 * @DI\Service(ClaimHydrator::SERVICE_NAME, public=true, shared=false)
 */
class ClaimHydrator
{
    const SERVICE_NAME = 'LaDanse.ClaimHydrator';

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

    /** @var array $claims */
    private $claims;

    /** @var array $playsRoles */
    private $playsRoles;

    /**
     * @return array
     */
    public function getCharacterIds(): array
    {
        return $this->characterIds;
    }

    /**
     * @param array $characterIds
     * @return ClaimHydrator
     */
    public function setCharacterIds(array $characterIds): ClaimHydrator
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
     * @return ClaimHydrator
     */
    public function setOnDateTime(\DateTime $onDateTime): ClaimHydrator
    {
        $this->onDateTime = $onDateTime;
        return $this;
    }

    public function hasBeenClaimed(int $characterId) : bool
    {
        $this->init();

        foreach($this->claims as $claim)
        {
            /** @var Entity\Claim $claim */
            if ($claim->getCharacter()->getId() == $characterId)
            {
                return true;
            }
        }

        return false;
    }

    /**
     * @param int $characterId
     *
     * @return Entity\Claim
     */
    public function getClaim(int $characterId)
    {
        $this->init();

        foreach($this->claims as $claim)
        {
            /** @var Entity\Claim $claim */
            if ($claim->getCharacter()->getId() == $characterId)
            {
                return $claim;
            }
        }

        return null;
    }

    public function getClaimedRoles(int $characterId) : array
    {
        $this->init();

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

    private function init()
    {
        if ($this->initialized)
            return;

        if ($this->getCharacterIds() == null || count($this->getCharacterIds()) == 0)
        {
            $this->claims = [];
            $this->playsRoles = [];
            $this->initialized = true;

            return;
        }

        $em = $this->doctrine->getManager();

        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->select('claim', 'account', 'character')
            ->from(Entity\Claim::class, 'claim')
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
                            $qb->expr()->lte('claim.fromTime', '?1'),
                            $qb->expr()->gt('claim.endTime', '?1')
                        ),
                        $qb->expr()->andX(
                            $qb->expr()->lte('claim.fromTime', '?1'),
                            $qb->expr()->isNull('claim.endTime')
                        )
                    )
                )
            )
            ->setParameter(1, $this->getOnDateTime());

        /* @var $query \Doctrine\ORM\Query */
        $query = $qb->getQuery();

        $this->claims = $query->getResult();

        $claimIds = [];

        foreach($this->claims as $claim)
        {
            /** @var Entity\Claim $claim */

            $claimIds[] = $claim->getId();
        }

        if (count($claimIds) == 0)
        {
            $this->playsRoles = [];
            $this->initialized = true;

            return;
        }

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

        $this->initialized = true;
    }
}