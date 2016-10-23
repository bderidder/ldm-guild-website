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
 * @DI\Service(GetAllCharactersInGuildQuery::SERVICE_NAME, public=true, shared=false)
 */
class GetAllCharactersInGuildQuery extends AbstractQuery
{
    const SERVICE_NAME = 'LaDanse.GetAllCharactersInGuildQuery';

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

    /** @var DTO\Reference\StringReference $guildReference */
    private $guildReference;

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
     * @return DTO\Reference\StringReference
     */
    public function getGuildReference(): DTO\Reference\StringReference
    {
        return $this->guildReference;
    }

    /**
     * @param DTO\Reference\StringReference $guildReference
     * @return GetAllCharactersInGuildQuery
     */
    public function setGuildReference(DTO\Reference\StringReference $guildReference): GetAllCharactersInGuildQuery
    {
        $this->guildReference = $guildReference;
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
     * @return GetAllCharactersInGuildQuery
     */
    public function setOnDateTime(\DateTime $onDateTime): GetAllCharactersInGuildQuery
    {
        $this->onDateTime = $onDateTime;
        return $this;
    }

    protected function validateInput()
    {
        if ($this->onDateTime == null
            || $this->guildReference == null
            || $this->guildReference->getId() == null)
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

        $qb->select('g', 'realm')
            ->from(Entity\GameData\Guild::class, 'g')
            ->join('g.realm', 'realm')
            ->where('g.id = ?1')
            ->setParameter(1, $this->getGuildReference()->getId());

        $this->logger->debug(
            __CLASS__ . " created DQL for retrieving GameRaces ",
            [
                "query" => $qb->getDQL()
            ]
        );

        /* @var $query \Doctrine\ORM\Query */
        $query = $qb->getQuery();

        $guilds = $query->getResult();

        if (count($guilds) != 1)
        {
            // throw exception
            return null;
        }

        /** @var Entity\GameData\Guild $guild */
        $guild = $guilds[0];

        if ($this->getOnDateTime() == null)
        {
            // when not set, initialize to right now
            $this->setOnDateTime(new \DateTime());
        }

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
                            ->from(Entity\InGuild::class, 'inGuild')
                            ->join('inGuild.character', 'innerCharacter')
                            ->add('where',
                                $qb->expr()->andX(
                                    $qb->expr()->eq('inGuild.guild', '?1'),
                                    $qb->expr()->orX(
                                        $qb->expr()->andX(
                                            $qb->expr()->lte('inGuild.fromTime', '?2'),
                                            $qb->expr()->gt('inGuild.endTime', '?2')
                                        ),
                                        $qb->expr()->andX(
                                            $qb->expr()->lte('inGuild.fromTime', '?2'),
                                            $qb->expr()->isNull('inGuild.endTime')
                                        )
                                    )
                                )
                            )->getDQL()
                    ),
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
            ->setParameter(1, $guild)
            ->setParameter(2, $this->getOnDateTime());

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
                ActivityType::QUERY_GET_ALL_CHARACTERS_IN_GUILD,
                $this->getAccount(),
                [
                    'accountId'      => $this->getAccount()->getId(),
                    'guildReference' => $this->getGuildReference(),
                    'onDateTime'     => $this->getOnDateTime()
                ]
            )
        );

        return DTO\Character\CharacterMapper::mapArray($characterVersions, $characterHydrator);
    }
}