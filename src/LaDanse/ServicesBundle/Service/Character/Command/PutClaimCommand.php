<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Service\Character\Command;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\Claim;
use LaDanse\DomainBundle\Entity\PlaysRole;
use LaDanse\DomainBundle\Entity\Role;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Common\AbstractCommand;
use LaDanse\ServicesBundle\Common\InvalidInputException;
use LaDanse\ServicesBundle\Common\ServiceException;
use LaDanse\ServicesBundle\Service\Authorization\AuthorizationService;
use LaDanse\ServicesBundle\Service\Authorization\ResourceByValue;
use LaDanse\ServicesBundle\Service\Authorization\SubjectReference;
use LaDanse\ServicesBundle\Service\Character\CharacterService;
use LaDanse\ServicesBundle\Service\DTO as DTO;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use LaDanse\DomainBundle\Entity as Entity;

/**
 * @DI\Service(PutClaimCommand::SERVICE_NAME, public=true, shared=false)
 */
class PutClaimCommand extends AbstractCommand
{
    const SERVICE_NAME = 'LaDanse.PutClaimCommand';

    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    public $logger;

    /**
     * @var EventDispatcherInterface $eventDispatcher
     * @DI\Inject("event_dispatcher")
     */
    public $eventDispatcher;

    /**
     * @var AuthorizationService $authzService
     * @DI\Inject(AuthorizationService::SERVICE_NAME)
     */
    public $authzService;

    /**
     * @var $logger \Doctrine\Bundle\DoctrineBundle\Registry
     * @DI\Inject("doctrine")
     */
    public $doctrine;

    /** @var int */
    private $characterId;

    /** @var DTO\Character\PatchClaim */
    private $patchClaim;

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
    public function getCharacterId(): int
    {
        return $this->characterId;
    }

    /**
     * @param int $characterId
     * @return PutClaimCommand
     */
    public function setCharacterId(int $characterId): PutClaimCommand
    {
        $this->characterId = $characterId;
        return $this;
    }

    /**
     * @return DTO\Character\PatchClaim
     */
    public function getPatchClaim()
    {
        return $this->patchClaim;
    }

    /**
     * @param DTO\Character\PatchClaim $patchClaim
     * @return PutClaimCommand
     */
    public function setPatchClaim($patchClaim)
    {
        $this->patchClaim = $patchClaim;
        return $this;
    }

    protected function validateInput()
    {
        if ($this->getPatchClaim() == null)
        {
            throw new InvalidInputException("patchClaim can't be null");
        }
    }

    /**
     * @return DTO\Character\Character|null
     * @throws ServiceException
     */
    protected function runCommand()
    {
        // create a shared $fromTime since we will need it often below
        $fromTime = new \DateTime();

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        /*
         * Search for an active claim for the given character
         * If found
         *      Verify if the current account is authorized to change it
         *      If yes
         *          Update it
         *      If no
         *          throw UnauthorizedException
         * Not found
         *      throw ServiceException (404)
         */

        /** @var QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->select('claim', 'character', 'account')
            ->from(Entity\Claim::class, 'claim')
            ->join('claim.character', 'character')
            ->join('claim.account', 'account')
            ->where('character.id = ?1')
            ->andWhere('claim.fromTime IS NOT NULL')
            ->andWhere('claim.endTime IS NULL')
            ->setParameter(1, $this->getCharacterId());

        /* @var Query $query */
        $query = $qb->getQuery();

        $claims = $query->getResult();

        if (count($claims) == 0)
        {
            throw new ServiceException(
                sprintf('Could not find an active claim for character %s', $this->getCharacterId()),
                404
            );
        }
        else if (count($claims) > 1)
        {
            throw new ServiceException(
                sprintf('There are too many claims for character %s', $this->getCharacterId()),
                500
            );
        }

        // count($claims) == 1, we can update a new Claim

        /** @var Claim $claim */
        $claim = $claims[0];

        /* verify that the user can edit this particular event */
        if (!$this->authzService->evaluate(
            new SubjectReference($this->getAccount()),
            ActivityType::CLAIM_EDIT,
            new ResourceByValue(Claim::class, $claim->getId(), $claim)))
        {
            $this->logger->warning(__CLASS__ . ' the user is not authorized to edit event in indexAction');

            throw new ServiceException(
                "You are not allowed to update this claim",
                403
            );
        }

        $claim
            ->setRaider($this->getPatchClaim()->isRaider())
            ->setComment($this->getPatchClaim()->getComment());

        $this->updatePlaysRoles($em, $claim, $fromTime);

        $em->flush();

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::CLAIM_EDIT,
                $this->getAccount(),
                [
                    'accountId'   => $this->getAccount()->getId(),
                    'characterId' => $this->getCharacterId(),
                    'patchClaim'  => ActivityEvent::annotatedToSimpleObject($this->getPatchClaim())
                ]
            )
        );

        /** @var CharacterService $characterService */
        $characterService = $this->container->get(CharacterService::SERVICE_NAME);

        return $characterService->getCharacterById($this->getCharacterId());
    }

    /**
     * @param EntityManager $em
     * @param Claim $claim
     * @param \DateTime $onDateTime
     *
     * @throws ServiceException
     */
    protected function updatePlaysRoles($em, $claim, $onDateTime)
    {
        $isDps = false;
        $isHealer = false;
        $isTank = false;

        foreach ($this->getPatchClaim()->getRoles() as $strRole) {
            $checkedRole = null;

            if ($strRole == Entity\Role::DPS)
            {
                $isDps = true;
            }
            else if ($strRole == Entity\Role::TANK)
            {
                $isTank = true;
            }
            else if ($strRole == Entity\Role::HEALER)
            {
                $isHealer = true;
            }
            else
            {
                throw new ServiceException(
                    sprintf("%s is not a recognized role", $strRole),
                    400
                );
            }
        }

        /** @var QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->select('playsRole')
            ->from(Entity\PlaysRole::class, 'playsRole')
            ->join('playsRole.claim', 'claim')
            ->where('claim.id = ?1')
            ->andWhere('playsRole.fromTime IS NOT NULL')
            ->andWhere('playsRole.endTime IS NULL')
            ->setParameter(1, $claim->getId());

        /* @var Query $query */
        $query = $qb->getQuery();

        $playsRoles = $query->getResult();

        $this->checkAndUpdateRole($em, $claim, Role::DPS, $isDps, $playsRoles, $onDateTime);
        $this->checkAndUpdateRole($em, $claim, Role::TANK, $isTank, $playsRoles, $onDateTime);
        $this->checkAndUpdateRole($em, $claim, Role::HEALER, $isHealer, $playsRoles, $onDateTime);
    }

    /**
     * @param EntityManager $em
     * @param Claim $claim
     * @param string $roleName
     * @param bool $willPlayRole
     * @param array $currentPlayRoles the list of roles currently associated with the claim
     * @param \DateTime $onDateTime
     */
    private function checkAndUpdateRole(EntityManager $em, Claim $claim, $roleName, $willPlayRole, $currentPlayRoles, $onDateTime)
    {
        $alreadyPlaysRole = false;

        /* @var $playsRole \LaDanse\DomainBundle\Entity\PlaysRole */
        foreach($claim->getRoles() as $playsRole)
        {
            // remember if the role is currently active (present and endTime not set)
            if ($playsRole->isRole($roleName) && ($playsRole->getEndTime() == null))
            {
                $alreadyPlaysRole = true;
            }
        }

        if (!$alreadyPlaysRole and $willPlayRole)
        {
            $em->persist($this->createPlaysRole($onDateTime, $claim, $roleName));

            $this->logger->info(__CLASS__ . ' added ' . $roleName . ' role to claim ' . $claim->getId());
        }

        /* @var $playsRole \LaDanse\DomainBundle\Entity\PlaysRole */
        foreach($claim->getRoles() as $playsRole)
        {
            // if the role is currently active (present and endTime not set)
            // and the player will not play it anymore, set endTime
            if ($playsRole->isRole($roleName) && ($playsRole->getEndTime() == null) && !$willPlayRole)
            {
                $playsRole->setEndTime($onDateTime);

                $this->logger->info(__CLASS__ . ' removed ' . $roleName . ' role from claim ' . $claim->getId());
            }
        }

        $em->flush();
    }

    private function createPlaysRole($onDateTime, $claim, $role)
    {
        $playsRole = new PlaysRole();
        $playsRole
            ->setRole($role)
            ->setClaim($claim)
            ->setFromTime($onDateTime);

        return $playsRole;
    }
}