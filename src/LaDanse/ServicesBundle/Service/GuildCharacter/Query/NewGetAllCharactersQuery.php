<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Service\GuildCharacter\Query;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\InGuild;
use LaDanse\ServicesBundle\Common\AbstractQuery;
use LaDanse\ServicesBundle\Common\InvalidInputException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use LaDanse\DomainBundle\Entity as Entity;
use LaDanse\ServicesBundle\Service\DTO as DTO;


/**
 * @DI\Service(NewGetAllCharactersQuery::SERVICE_NAME, public=true, shared=false)
 */
class NewGetAllCharactersQuery extends AbstractQuery
{
    const SERVICE_NAME = 'LaDanse.NewGetAllCharactersQuery';

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
     * @return NewGetAllCharactersQuery
     */
    public function setGuildReference(DTO\Reference\StringReference $guildReference): NewGetAllCharactersQuery
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
     * @return NewGetAllCharactersQuery
     */
    public function setOnDateTime(\DateTime $onDateTime): NewGetAllCharactersQuery
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
                        'characterVersion.id',
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

        return DTO\Character\CharacterMapper::mapArray($query->getResult(), $guild);
    }
}