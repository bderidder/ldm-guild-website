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
 * @DI\Service(CharactersByKeywordsQuery::SERVICE_NAME, public=true, shared=false)
 */
class CharactersByKeywordsQuery extends AbstractQuery
{
    const SERVICE_NAME = 'LaDanse.CharactersByKeywordsQuery';

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

    /** @var string $keywords */
    private $keywords;

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
     * @return string
     */
    public function getKeywords(): string
    {
        return $this->keywords;
    }

    /**
     * @param string $keywords
     * @return CharactersByKeywordsQuery
     */
    public function setKeywords(string $keywords): CharactersByKeywordsQuery
    {
        $this->keywords = $keywords;
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
     * @return CharactersByKeywordsQuery
     */
    public function setOnDateTime(\DateTime $onDateTime): CharactersByKeywordsQuery
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

        $qb->select('characterVersion', 'character', 'realm', 'gameClass', 'gameRace')
            ->from(Entity\CharacterVersion::class, 'characterVersion')
            ->join('characterVersion.character', 'character')
            ->join('characterVersion.gameClass', 'gameClass')
            ->join('characterVersion.gameRace', 'gameRace')
            ->join('character.realm', 'realm')
            ->add('where',
                $qb->expr()->andX(
                    $qb->expr()->like('character.name', ':keywords'),
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
            ->setMaxResults(50)
            ->setParameter('onDateTime', $this->getOnDateTime())
            ->setParameter('keywords', '%' . $this->getKeywords() . '%');

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
                ActivityType::QUERY_CHARACTERS_BY_KEYWORDS,
                $this->isAuthenticated() ? $this->getAccount() : null,
                [
                    'accountId'  => $this->isAuthenticated() ? $this->getAccount()->getId() : null,
                    'keywords'   => $this->getKeywords(),
                    'onDateTime' => $this->getOnDateTime()
                ]
            )
        );

        return DTO\Character\CharacterMapper::mapArray($characterVersions, $characterHydrator);
    }
}