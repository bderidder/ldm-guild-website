<?php

namespace LaDanse\ServicesBundle\Service\GuildCharacter\Query;

use JMS\DiExtraBundle\Annotation as DI;

use LaDanse\CommonBundle\Helper\AbstractQuery;

use LaDanse\DomainBundle\Entity\Character;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @DI\Service(GuildCharacterQuery::SERVICE_NAME, public=true, scope="prototype")
 */
class GuildCharacterQuery extends AbstractQuery
{
    const SERVICE_NAME = 'LaDanse.GuildCharacterQuery';

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

    /** @var $characterId int */
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

        $this->setOnDateTime(null);
    }

    /**
     * @return int
     */
    public function getCharacterId()
    {
        return $this->characterId;
    }

    /**
     * @param int $characterId
     */
    public function setCharacterId($characterId)
    {
        $this->characterId = $characterId;
    }

    /**
     * @return \DateTime
     */
    public function getOnDateTime()
    {
        return $this->onDateTime;
    }

    /**
     * @param \DateTime $onDateTime
     */
    public function setOnDateTime($onDateTime)
    {
        $this->onDateTime = $onDateTime;
    }

    protected function validateInput()
    {

    }

    protected function runQuery()
    {
        if ($this->getOnDateTime() == null)
        {
            // when not set, initialize to right now
            $this->setOnDateTime(new \DateTime());
        }

        $em = $this->doctrine->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery(
            $this->createSQLFromTemplate('LaDanseDomainBundle::selectGuildCharacter.sql.twig')
        );
        $query->setParameter('characterName', $this->getCharacterId());
        $query->setParameter('onDateTime', $this->getOnDateTime());

        $characters = $query->getResult();

        if (count($characters) == 0)
        {
            throw new \Exception('No character could be found at that time.');
        }

        $character = $characters[0];

        return $this->characterToDto($character, $this->getOnDateTime());
    }

    /**
     * @param Character $character
     * @param \DateTime $onDateTime
     *
     * @return object
     */
    protected function characterToDto(Character $character, \DateTime $onDateTime)
    {
        $activeVersion = $character->getVersionForDate($onDateTime);

        if (is_null($activeVersion))
        {
            $this->logger->info('no active version found for ' . $onDateTime->format("d/M/Y"));

            return (object)array(
                "id"       => $character->getId(),
                "fromTime" => $character->getFromTime(),
                "name"     => $character->getName()
            );
        }
        else
        {
            $this->logger->info('active version found for ' . $onDateTime->format("d/M/Y"));

            return (object)array(
                "id"       => $character->getId(),
                "fromTime" => $character->getFromTime(),
                "name"     => $character->getName(),
                "level"    => $activeVersion->getLevel(),
                "class"    => (object)array(
                    "id"   => $activeVersion->getGameClass()->getId(),
                    "name" => $activeVersion->getGameClass()->getName()
                ),
                "race"     => (object)array(
                    "id"   => $activeVersion->getGameRace()->getId(),
                    "name" => $activeVersion->getGameRace()->getName()
                )
            );
        }
    }
}