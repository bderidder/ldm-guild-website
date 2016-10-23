<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Service\Character\Query;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\InGuild;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Common\AbstractQuery;
use LaDanse\ServicesBundle\Common\InvalidInputException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use LaDanse\DomainBundle\Entity as Entity;
use LaDanse\ServicesBundle\Service\DTO as DTO;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @DI\Service(CharactersClaimedByAccountQuery::SERVICE_NAME, public=true, shared=false)
 */
class CharactersClaimedByAccountQuery extends AbstractQuery
{
    const SERVICE_NAME = 'LaDanse.CharactersClaimedByAccountQuery';

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

    /** @var int $accountId */
    private $accountId;

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
     * @return int
     */
    public function getAccountId(): int
    {
        return $this->accountId;
    }

    /**
     * @param int $accountId
     * @return CharactersClaimedByAccountQuery
     */
    public function setAccountId(int $accountId): CharactersClaimedByAccountQuery
    {
        $this->accountId = $accountId;
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
     * @return CharactersClaimedByAccountQuery
     */
    public function setOnDateTime(\DateTime $onDateTime): CharactersClaimedByAccountQuery
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

        /** @var \Doctrine\ORM\QueryBuilder $innerQb */
        $innerQb = $em->createQueryBuilder();

        $qb->select('characterVersion', 'character', 'realm', 'gameClass', 'gameRace')
            ->from(Entity\CharacterVersion::class, 'characterVersion')
            ->join('characterVersion.character', 'character')
            ->join('characterVersion.gameClass', 'gameClass')
            ->join('characterVersion.gameRace', 'gameRace')
            ->join('character.realm', 'realm')
            ->add('where',
                $qb->expr()->andX(
                    $qb->expr()->in(
                        'characterVersion.character',
                        $innerQb->select('innerCharacter.id')
                            ->from(Entity\Claim::class, 'innerClaim')
                            ->join('innerClaim.character', 'innerCharacter')
                            ->where(
                                $innerQb->expr()->andX(
                                    $innerQb->expr()->eq('innerClaim.account', ':account'),
                                    $innerQb->expr()->orX(
                                        $innerQb->expr()->andX(
                                            $innerQb->expr()->lte('innerClaim.fromTime', ':onDateTime'),
                                            $innerQb->expr()->gt('innerClaim.endTime', ':onDateTime')
                                        ),
                                        $innerQb->expr()->andX(
                                            $innerQb->expr()->lte('innerClaim.fromTime', ':onDateTime'),
                                            $innerQb->expr()->isNull('innerClaim.endTime')
                                        )
                                    )
                                )
                            )->getDQL()
                    ),
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
            )
            ->addOrderBy('character.name')
            ->setParameter('onDateTime', $this->getOnDateTime())
            ->setParameter('account', $em->getReference(Entity\Account::class, $this->getAccountId()));

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

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::QUERY_CHARACTERS_CLAIMED_BY_ACCOUNT,
                $this->getAccount(),
                [
                    'accountId'      => $this->getAccount()->getId()
                ]
            )
        );

        return DTO\Character\CharacterMapper::mapArray($characterVersions, $characterHydrator);
    }
}