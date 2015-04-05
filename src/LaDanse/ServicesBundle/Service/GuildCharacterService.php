<?php

namespace LaDanse\ServicesBundle\Service;

use LaDanse\CommonBundle\Helper\LaDanseService;
use LaDanse\DomainBundle\Entity\Character;
use LaDanse\DomainBundle\Entity\GameClass;
use LaDanse\DomainBundle\Entity\GameRace;
use LaDanse\DomainBundle\Entity\Role;
use LaDanse\ServicesBundle\Service\GuildCharacter\AllGuildCharactersQuery;
use LaDanse\ServicesBundle\Service\GuildCharacter\CreateCharacterCommand;
use LaDanse\ServicesBundle\Service\GuildCharacter\CreateClaimCommand;
use LaDanse\ServicesBundle\Service\GuildCharacter\EndCharacterCommand;
use LaDanse\ServicesBundle\Service\GuildCharacter\EndClaimCommand;
use LaDanse\ServicesBundle\Service\GuildCharacter\UpdateCharacterCommand;
use LaDanse\ServicesBundle\Service\GuildCharacter\UpdateClaimCommand;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service(GuildCharacterService::SERVICE_NAME, public=true)
 */
class GuildCharacterService extends LaDanseService
{
    const SERVICE_NAME = 'LaDanse.GuildCharacterService';

    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    public $logger;

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

    public function getAllGuildCharacters(\DateTime $onDateTime = null)
    {
        /** @var $allGuildCharactersQuery AllGuildCharactersQuery */
        $allGuildCharactersQuery = $this->get(AllGuildCharactersQuery::SERVICE_NAME);

        $allGuildCharactersQuery->setOnDateTime($onDateTime);

        return $allGuildCharactersQuery->run();
    }

    public function getGuildCharacter($characterId, \DateTime $onDateTime = null)
    {
        if ($onDateTime == null)
        {
            // when not set, initialize to right now
            $onDateTime = new \DateTime();
        }

        $em = $this->getDoctrine()->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery(
            $this->createSQLFromTemplate('LaDanseDomainBundle::selectGuildCharacter.sql.twig')
        );
        $query->setParameter('characterName', $characterId);
        $query->setParameter('onDateTime', $onDateTime);
        
        $characters = $query->getResult();

        if (count($characters) == 0)
        {
            throw new \Exception('No character could be found at that time.');
        }

        $character = $characters[0];

        return $this->characterToDto($character, $onDateTime);
    }

    public function getClaims($accountId, \DateTime $onDateTime = null)
    {
        if ($onDateTime == null)
        {
            // when not set, initialize to right now
            $onDateTime = new \DateTime();
        }

        $em = $this->getDoctrine()->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery(
            $this->createSQLFromTemplate('LaDanseDomainBundle::selectClaimsForAccount.sql.twig')
        );
        $query->setParameter('accountId', $accountId);
        $query->setParameter('onDateTime', $onDateTime);
        
        $claims = $query->getResult();

        $claimsModels = array();

        foreach($claims as $claim)
        {
            $claimsModels[] = $this->claimToDto($claim, $onDateTime);
        }

        return $claimsModels;
    }

    public function getClaimsForCharacter($characterName, \DateTime $onDateTime = null)
    {
        if ($onDateTime == null)
        {
            // when not set, initialize to right now
            $onDateTime = new \DateTime();
        }

        $em = $this->getDoctrine()->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery(
            $this->createSQLFromTemplate('LaDanseDomainBundle::selectClaimsForCharacter.sql.twig')
        );
        $query->setParameter('characterName', $characterName);
        $query->setParameter('onDateTime', $onDateTime);
        
        $claims = $query->getResult();

        $claimsModels = array();

        foreach($claims as $claim)
        {
            $claimsModels[] = $this->claimToDto($claim, $onDateTime);
        }

        return $claimsModels;
    }

    public function getClaim($claimId, \DateTime $onDateTime = null)
    {
        if ($onDateTime == null)
        {
            // when not set, initialize to right now
            $onDateTime = new \DateTime();
        }

        $em = $this->getDoctrine()->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery(
            $this->createSQLFromTemplate('LaDanseDomainBundle::selectActiveClaim.sql.twig')
        );
        $query->setParameter('claimId', $claimId);
        
        $claims = $query->getResult();

        if (count($claims) == 0)
        {
            return null;
        }

        $claim = $claims[0];
        
        $claimsModel = $this->claimToDto($claim, $onDateTime);
       
        return $claimsModel;
    }

    public function getAllCharacters(\DateTime $onDateTime = null)
    {
        if ($onDateTime == null)
        {
            // when not set, initialize to right now
            $onDateTime = new \DateTime();
        }

        $em = $this->getDoctrine()->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery(
            $this->createSQLFromTemplate('LaDanseDomainBundle::selectCharactersForAccount.sql.twig')
        );

        $query->setParameter('onDateTime', $onDateTime);

        $characters = $query->getResult();

        return $this->charactersToDtoArray($characters, $onDateTime);
    }

    public function getUnclaimedCharacters(\DateTime $onDateTime = null)
    {
        if ($onDateTime == null)
        {
            // when not set, initialize to right now
            $onDateTime = new \DateTime();
        }

        $em = $this->getDoctrine()->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery(
            $this->createSQLFromTemplate('LaDanseDomainBundle::selectUnclaimedCharacters.sql.twig')
        );
        $query->setParameter('onDateTime', $onDateTime);

        $characters = $query->getResult();

        return $this->charactersToDtoArray($characters, $onDateTime);
    }

    public function getActiveClaimsForAccount($account, \DateTime $onDateTime = null)
    {

    }

    public function getActiveClaimsForCharacter($character, \DateTime $onDateTime = null)
    {

    }

    public function createClaim($accountId, $characterId, $playsTank, $playsHealer, $playsDPS)
    {
        /** @var $createClaimCommand CreateClaimCommand */
        $createClaimCommand = $this->get(CreateClaimCommand::SERVICE_NAME);

        $createClaimCommand->setAccountId($accountId);
        $createClaimCommand->setCharacterId($characterId);
        $createClaimCommand->setPlaysTank($playsTank);
        $createClaimCommand->setPlaysHealer($playsHealer);
        $createClaimCommand->setPlaysDPS($playsDPS);

        $createClaimCommand->run();
    }

    public function updateClaim($claimId, $playsTank, $playsHealer, $playsDPS)
    {
        /** @var $updateClaimCommand UpdateClaimCommand */
        $updateClaimCommand = $this->get(UpdateClaimCommand::SERVICE_NAME);

        $updateClaimCommand->setClaimId($claimId);
        $updateClaimCommand->setPlaysTank($playsTank);
        $updateClaimCommand->setPlaysHealer($playsHealer);
        $updateClaimCommand->setPlaysDPS($playsDPS);

        $updateClaimCommand->run();
    }

    /**
     * @param int $claimId
     */
    public function endClaim($claimId)
    {
        /** @var $endClaimCommand EndClaimCommand */
        $endClaimCommand = $this->get(EndClaimCommand::SERVICE_NAME);

        $endClaimCommand->setClaimId($claimId);

        $endClaimCommand->run();
    }

    /**
     * @param string $name
     * @param int $level
     * @param GameRace $gameRace
     * @param GameClass $gameClass
     */
    public function createCharacter($name, $level, GameRace $gameRace, GameClass $gameClass)
    {
        /** @var $createCharacterCommand CreateCharacterCommand */
        $createCharacterCommand = $this->get(CreateCharacterCommand::SERVICE_NAME);

        $createCharacterCommand->setName($name);
        $createCharacterCommand->setLevel($level);
        $createCharacterCommand->setGameRace($gameRace);
        $createCharacterCommand->setGameClass($gameClass);

        $createCharacterCommand->run();
    }

    /**
     * @param int $characterId
     * @param string $name
     * @param int $level
     * @param GameRace $gameRace
     * @param GameClass $gameClass
     */
    public function updateCharacter($characterId, $name, $level, GameRace $gameRace, GameClass $gameClass)
    {
        /** @var $updateCharacterCommand UpdateCharacterCommand */
        $updateCharacterCommand = $this->get(UpdateCharacterCommand::SERVICE_NAME);

        $updateCharacterCommand->setCharacterId($characterId);
        $updateCharacterCommand->setName($name);
        $updateCharacterCommand->setLevel($level);
        $updateCharacterCommand->setGameRace($gameRace);
        $updateCharacterCommand->setGameClass($gameClass);

        $updateCharacterCommand->run();
    }

    public function endCharacter($characterId)
    {
        /** @var $endCharacterCommand EndCharacterCommand */
        $endCharacterCommand = $this->get(EndCharacterCommand::SERVICE_NAME);

        $endCharacterCommand->setCharacterId($characterId);

        $endCharacterCommand->run();
    }

    protected function charactersToDtoArray($characters, \DateTime $onDateTime)
    {
        $charactersDto = array();

        foreach($characters as $character)
        {
            $charactersDto[] = $this->characterToDto($character, $onDateTime);
        }

        return $charactersDto;
    }

    /* @var $claim \LaDanse\DomainBundle\Entity\Claim
     * @var $onDateTime \DateTime
     * @return object
     */
    protected function claimToDto($claim, \DateTime $onDateTime)
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

    /**
     * @param Character $character
     * @param \DateTime $onDateTime
     *
     * @return object
     */
    protected function characterToDto($character, \DateTime $onDateTime)
    {
        $versions = $character->getVersions();

        $activeVersion = $versions[count($versions) - 1];

        foreach($versions as $version)
        {
            if ((($version->getFromTime() <= $onDateTime) == 0)
                and ((($version->getEndTime() > $onDateTime) == 0) or is_null($version->getEndTime())))
            {
                $activeVersion = $version;
            }
        }

        return (object)array(
            "id"    => $character->getId(),
            "fromTime"  => $character->getFromTime(),
            "name"  => $character->getName(),
            "level" => $activeVersion->getLevel(),
            "class" => (object)array(
                "id"   => $activeVersion->getGameClass()->getId(),
                "name" => $activeVersion->getGameClass()->getName()
            ),
            "race"  => (object)array(
                "id"   => $activeVersion->getGameRace()->getId(),
                "name" => $activeVersion->getGameRace()->getName()
            )
        );
    }
}