<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Service\Character\Query;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Common\AbstractQuery;
use LaDanse\ServicesBundle\Common\InvalidInputException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use LaDanse\DomainBundle\Entity as Entity;
use LaDanse\ServicesBundle\Service\DTO as DTO;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @DI\Service(GetAllClaimedCharactersQuery::SERVICE_NAME, public=true, shared=false)
 */
class GetAllClaimedCharactersQuery extends AbstractQuery
{
    const SERVICE_NAME = 'LaDanse.GetAllClaimedCharactersQuery';

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
     * @return \DateTime
     */
    public function getOnDateTime(): \DateTime
    {
        return $this->onDateTime;
    }

    /**
     * @param \DateTime $onDateTime
     * @return GetAllClaimedCharactersQuery
     */
    public function setOnDateTime(\DateTime $onDateTime): GetAllClaimedCharactersQuery
    {
        $this->onDateTime = $onDateTime;
        return $this;
    }

    /**
     * @throws InvalidInputException
     */
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

    /**
     * @return array
     * @throws DTO\MapperException
     */
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
            ->setParameter('onDateTime', $this->getOnDateTime());

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
                $this->isAuthenticated() ? $this->getAccount() : null,
                [
                    'accountId' => $this->isAuthenticated() ? $this->getAccount()->getId() : null
                ]
            )
        );

        return DTO\Character\CharacterMapper::mapArray($characterVersions, $characterHydrator);
    }
}