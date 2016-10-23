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
 * @DI\Service(DeleteClaimCommand::SERVICE_NAME, public=true, shared=false)
 */
class DeleteClaimCommand extends AbstractCommand
{
    const SERVICE_NAME = 'LaDanse.DeleteClaimCommand';

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
     * @return DeleteClaimCommand
     */
    public function setCharacterId(int $characterId): DeleteClaimCommand
    {
        $this->characterId = $characterId;
        return $this;
    }

    protected function validateInput()
    {
    }

    protected function runCommand()
    {
        // create a shared $fromTime since we will need it often below
        $onDateTime = new \DateTime();

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        /*
         * Search for an active claim for the given character
         * If found
         *      Verify if the current account is authorized to delete it
         *      If yes
         *          set the endTime on the claim and any playsRole
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

        // count($claims) == 1, we can remove the Claim

        /** @var Claim $claim */
        $claim = $claims[0];

        /* verify that the user can edit this particular event */
        if (!$this->authzService->evaluate(
            new SubjectReference($this->getAccount()),
            ActivityType::CLAIM_REMOVE,
            new ResourceByValue(Claim::class, $claim->getId(), $claim)))
        {
            $this->logger->warning(__CLASS__ . ' the user is not authorized to edit event in indexAction');

            throw new ServiceException(
                "You are not allowed to update this claim",
                403
            );
        }

        $claim->setEndTime($onDateTime);

        /** @var QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->update(Entity\PlaysRole::class, 'playsRole')
            ->set('playsRole.endTime', '?1')
            ->where($qb->expr()->eq('playsRole.claim', '?2'))
            ->andWhere('playsRole.endTime IS NULL')
            ->setParameter(1, $onDateTime)
            ->setParameter(2, $claim)
            ->getQuery()->execute();

        $em->flush();

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::CLAIM_REMOVE,
                $this->getAccount(),
                [
                    'accountId'   => $this->getAccount()->getId(),
                    'characterId' => $this->getCharacterId()
                ]
            )
        );

        /** @var CharacterService $characterService */
        $characterService = $this->container->get(CharacterService::SERVICE_NAME);

        return $characterService->getCharacterById($this->getCharacterId());
    }
}