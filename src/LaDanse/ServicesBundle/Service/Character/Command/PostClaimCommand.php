<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Service\Character\Command;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\Claim;
use LaDanse\DomainBundle\Entity\PlaysRole;
use LaDanse\ServicesBundle\Common\AbstractCommand;
use LaDanse\ServicesBundle\Common\InvalidInputException;
use LaDanse\ServicesBundle\Common\ServiceException;
use LaDanse\ServicesBundle\Service\Character\CharacterService;
use LaDanse\ServicesBundle\Service\DTO as DTO;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use LaDanse\DomainBundle\Entity as Entity;

/**
 * @DI\Service(PostClaimCommand::SERVICE_NAME, public=true, shared=false)
 */
class PostClaimCommand extends AbstractCommand
{
    const SERVICE_NAME = 'LaDanse.PostClaimCommand';

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
     * @var $logger \Doctrine\Bundle\DoctrineBundle\Registry
     * @DI\Inject("doctrine")
     */
    public $doctrine;

    /** @var int */
    private $characterId;

    /** @var DTO\Character\PatchClaim */
    private $patchClaim;

    /** @var int */
    private $accountId;

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
     * @return PostClaimCommand
     */
    public function setCharacterId(int $characterId): PostClaimCommand
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
     * @param mixed $patchClaim
     * @return PostClaimCommand
     */
    public function setPatchClaim($patchClaim)
    {
        $this->patchClaim = $patchClaim;
        return $this;
    }

    /**
     * @return int
     */
    public function getAccountId(): int
    {
        return $this->accountId;
    }

    /**
     * @param int $accountId
     * @return PostClaimCommand
     */
    public function setAccountId(int $accountId): PostClaimCommand
    {
        $this->accountId = $accountId;
        return $this;
    }

    protected function validateInput()
    {
        if ($this->getPatchClaim() == null)
        {
            throw new InvalidInputException("patchClaim can't be null");
        }
    }

    protected function runCommand()
    {
        // create a shared $fromTime since we will need it often below
        $fromTime = new \DateTime();

        $em = $this->doctrine->getManager();

        /*
         * Verify if there is an active claim for this character
         *  If yes
         *      Throw exception that the character is already claimed
         *  If no
         *      Create claim using the information found in PatchClaim
         *
         */

        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->select('claim', 'character', 'account')
            ->from(Entity\Claim::class, 'claim')
            ->join('claim.character', 'character')
            ->join('claim.account', 'account')
            ->where('character.id = ?1')
            ->andWhere('claim.fromTime IS NOT NULL')
            ->andWhere('claim.endTime IS NULL')
            ->setParameter(1, $this->getCharacterId());

        /* @var $query \Doctrine\ORM\Query */
        $query = $qb->getQuery();

        $claims = $query->getResult();

        if (count($claims) == 1)
        {
            throw new ServiceException(
                'There is already a claim for this character',
                400
            );
        }
        else if (count($claims) > 1)
        {
            throw new ServiceException(
                'There are multiple active claims for this character',
                500
            );
        }

        // count($claims) == 0, we can create a new Claim

        $claim = new Claim();
        $claim
            ->setRaider($this->getPatchClaim()->isRaider())
            ->setComment($this->getPatchClaim()->getComment())
            ->setAccount($em->getReference(Entity\Account::class, $this->getAccountId()))
            ->setCharacter($em->getReference(Entity\Character::class, $this->getCharacterId()))
            ->setFromTime($fromTime)
            ->setEndTime(null);

        $this->persistPlaysRoles($em, $fromTime, $claim);

        $em->persist($claim);
        $em->flush();

        /** @var CharacterService $characterService */
        $characterService = $this->container->get(CharacterService::SERVICE_NAME);

        return $characterService->getCharacterById($this->getCharacterId());
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

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $em
     * @param \DateTime $fromTime
     * @param Claim $claim
     *
     * @throws ServiceException
     */
    protected function persistPlaysRoles($em, $fromTime, $claim)
    {
        $isDps = false;
        $isHealer = false;
        $isTank = false;

        foreach ($this->getPatchClaim()->getRoles() as $strRole) {
            $checkedRole = null;

            if ($strRole == Entity\Role::DPS) {
                if ($isDps)
                    throw new ServiceException(
                        sprintf("role %s can only be claimed once", $strRole),
                        400
                    );

                $checkedRole = Entity\Role::DPS;

                $isDps = true;
            } else if ($strRole == Entity\Role::TANK) {
                if ($isTank)
                    throw new ServiceException(
                        sprintf("role %s can only be claimed once", $strRole),
                        400
                    );

                $checkedRole = Entity\Role::TANK;

                $isTank = true;
            } else if ($strRole == Entity\Role::HEALER) {
                if ($isHealer)
                    throw new ServiceException(
                        sprintf("role %s can only be claimed once", $strRole),
                        400
                    );

                $checkedRole = Entity\Role::HEALER;

                $isHealer = true;
            } else {
                throw new ServiceException(
                    sprintf("%s is not a recognized role", $strRole),
                    400
                );
            }

            $em->persist($this->createPlaysRole($fromTime, $claim, $checkedRole));
        }
    }
}