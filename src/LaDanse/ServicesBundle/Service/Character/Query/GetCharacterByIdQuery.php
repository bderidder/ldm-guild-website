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
use LaDanse\ServicesBundle\Common\ServiceException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use LaDanse\DomainBundle\Entity as Entity;
use LaDanse\ServicesBundle\Service\DTO as DTO;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @DI\Service(GetCharacterByIdQuery::SERVICE_NAME, public=true, shared=false)
 */
class GetCharacterByIdQuery extends AbstractQuery
{
    const SERVICE_NAME = 'LaDanse.GetCharacterByIdQuery';

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

    /** @var int $characterId */
    private $characterId;

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
    public function getCharacterId(): int
    {
        return $this->characterId;
    }

    /**
     * @param int $characterId
     * @return GetCharacterByIdQuery
     */
    public function setCharacterId(int $characterId): GetCharacterByIdQuery
    {
        $this->characterId = $characterId;
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
     * @return GetCharacterByIdQuery
     */
    public function setOnDateTime(\DateTime $onDateTime): GetCharacterByIdQuery
    {
        $this->onDateTime = $onDateTime;
        return $this;
    }

    protected function validateInput()
    {
    }

    protected function runQuery()
    {
        $em = $this->doctrine->getManager();

        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->select('characterVersion', 'character', 'realm', 'gameClass', 'gameRace')
            ->from(Entity\CharacterVersion::class, 'characterVersion')
            ->join('characterVersion.character', 'character')
            ->join('characterVersion.gameClass', 'gameClass')
            ->join('characterVersion.gameRace', 'gameRace')
            ->join('character.realm', 'realm')
            ->add('where',
                $qb->expr()->andX(
                    $qb->expr()->eq('character.id', '?1'),
                    $qb->expr()->orX(
                        $qb->expr()->andX(
                            $qb->expr()->lte('characterVersion.fromTime', '?2'),
                            $qb->expr()->gt('characterVersion.endTime', '?2')
                        ),
                        $qb->expr()->andX(
                            $qb->expr()->lte('characterVersion.fromTime', '?2'),
                            $qb->expr()->isNull('characterVersion.endTime')
                        )
                    )
                )
            )
            ->setParameter(1, $this->getCharacterId())
            ->setParameter(2, $this->getOnDateTime());

        /* @var $query \Doctrine\ORM\Query */
        $query = $qb->getQuery();

        $characterVersions = $query->getResult();

        if (count($characterVersions) != 1)
        {
            throw new ServiceException(
                sprintf(
                    "A character with id %s was not found",
                    $this->getCharacterId()
                ),
                500
            );
        }

        /** @var Entity\CharacterVersion $characterVersion */
        $characterVersion = $characterVersions[0];

        /** @var CharacterHydrator $characterHydrator */
        $characterHydrator = $this->container->get(CharacterHydrator::SERVICE_NAME);
        $characterHydrator->setCharacterIds([$this->getCharacterId()]);
        $characterHydrator->setOnDateTime($this->getOnDateTime());

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::QUERY_GET_CHARACTER_BY_ID,
                $this->isAuthenticated() ? $this->getAccount() : null,
                [
                    'accountId'   => $this->isAuthenticated() ? $this->getAccount()->getId() : null,
                    'characterId' => $this->getCharacterId(),
                    'onDateTime'  => $this->getOnDateTime()
                ]
            )
        );

        return DTO\Character\CharacterMapper::mapSingle($characterVersion, $characterHydrator);
    }
}