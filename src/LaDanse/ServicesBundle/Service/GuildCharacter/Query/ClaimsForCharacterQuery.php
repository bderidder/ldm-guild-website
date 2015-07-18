<?php

namespace LaDanse\ServicesBundle\Service\GuildCharacter\Query;

use LaDanse\DomainBundle\Entity\Claim;
use LaDanse\DomainBundle\Entity\Role;
use Symfony\Component\DependencyInjection\ContainerInterface;

use LaDanse\CommonBundle\Helper\AbstractQuery;

use LaDanse\DomainBundle\Entity\Character;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service(ClaimsForCharacterQuery::SERVICE_NAME, public=true, scope="prototype")
 */
class ClaimsForCharacterQuery extends AbstractQuery
{
    const SERVICE_NAME = 'LaDanse.ClaimsForCharacterQuery';

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

    /** @var $characterName string */
    private $characterName;

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
     * @return string
     */
    public function getCharacterName()
    {
        return $this->characterName;
    }

    /**
     * @param string $characterName
     */
    public function setCharacterName($characterName)
    {
        $this->characterName = $characterName;
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
            $this->createSQLFromTemplate('LaDanseDomainBundle::selectClaimsForCharacter.sql.twig')
        );
        $query->setParameter('characterName', $this->getCharacterName());
        $query->setParameter('onDateTime', $this->getOnDateTime());

        $claims = $query->getResult();

        $claimsModels = array();

        foreach($claims as $claim)
        {
            $claimsModels[] = $this->claimToDto($claim, $this->getOnDateTime());
        }

        return $claimsModels;
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

    /**
     * @var $claim Claim
     * @var $onDateTime \DateTime
     *
     * @return object
     */
    protected function claimToDto(Claim $claim, \DateTime $onDateTime)
    {
        return (object)array(
            "id"          => $claim->getId(),
            "character"   => $this->characterToDto($claim->getCharacter(), $onDateTime),
            "fromTime"    => $claim->getFromTime(),
            "playsTank"   => $claim->containsRole(Role::TANK, $onDateTime),
            "playsHealer" => $claim->containsRole(Role::HEALER, $onDateTime),
            "playsDPS"    => $claim->containsRole(Role::DPS, $onDateTime),
        );
    }
}