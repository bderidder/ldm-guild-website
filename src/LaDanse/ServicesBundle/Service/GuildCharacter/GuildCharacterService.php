<?php

namespace LaDanse\ServicesBundle\Service\GuildCharacter;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\GameClass;
use LaDanse\DomainBundle\Entity\GameRace;
use LaDanse\ServicesBundle\Common\LaDanseService;
use LaDanse\ServicesBundle\Service\GuildCharacter\Command\CreateCharacterCommand;
use LaDanse\ServicesBundle\Service\GuildCharacter\Command\CreateClaimCommand;
use LaDanse\ServicesBundle\Service\GuildCharacter\Command\EndCharacterCommand;
use LaDanse\ServicesBundle\Service\GuildCharacter\Command\EndClaimCommand;
use LaDanse\ServicesBundle\Service\GuildCharacter\Command\UpdateCharacterCommand;
use LaDanse\ServicesBundle\Service\GuildCharacter\Command\UpdateClaimCommand;
use LaDanse\ServicesBundle\Service\GuildCharacter\Query\AllActiveClaimsQuery;
use LaDanse\ServicesBundle\Service\GuildCharacter\Query\AllGuildCharactersQuery;
use LaDanse\ServicesBundle\Service\GuildCharacter\Query\ClaimForIdQuery;
use LaDanse\ServicesBundle\Service\GuildCharacter\Query\ClaimsForAccountQuery;
use LaDanse\ServicesBundle\Service\GuildCharacter\Query\GuildCharacterQuery;
use LaDanse\ServicesBundle\Service\GuildCharacter\Query\UnclaimedCharactersQuery;
use Symfony\Component\DependencyInjection\ContainerInterface;

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

    /**
     * Returns an array of all characters in the guild
     * Properties are taken as valid on the given $onDateTime
     *
     * @param \DateTime $onDateTime if left null, the current date and time is used
     *
     * @return mixed
     */
    public function getAllGuildCharacters(\DateTime $onDateTime = null)
    {
        /** @var $allGuildCharactersQuery AllGuildCharactersQuery */
        $allGuildCharactersQuery = $this->get(AllGuildCharactersQuery::SERVICE_NAME);

        $allGuildCharactersQuery->setOnDateTime($onDateTime);

        return $allGuildCharactersQuery->run();
    }

    /**
     * Returns the properties of the character with given $characterId
     * Properties are taken as valid on the given $onDateTime
     *
     * @param $characterId
     * @param \DateTime $onDateTime if left null, the current date and time is used
     *
     * @return mixed
     */
    public function getGuildCharacter($characterId, \DateTime $onDateTime = null)
    {
        /** @var $guildCharacterQuery GuildCharacterQuery */
        $guildCharacterQuery = $this->get(GuildCharacterQuery::SERVICE_NAME);

        $guildCharacterQuery->setCharacterId($characterId);
        $guildCharacterQuery->setOnDateTime($onDateTime);

        return $guildCharacterQuery->run();
    }

    /**
     * Returns all guild character claims that are currently active
     *
     * @return array
     */
    public function getAllActiveClaims()
    {
        /** @var AllActiveClaimsQuery $allActiveClaimsQuery */
        $allActiveClaimsQuery = $this->get(AllActiveClaimsQuery::SERVICE_NAME);

        return $allActiveClaimsQuery->run();
    }

    /**
     * Returns all claims for a given account that are active on the given $onDateTime
     *
     * @param $accountId
     * @param \DateTime $onDateTime if left null, the current date and time is used
     *
     * @return array
     */
    public function getClaimsForAccount($accountId, \DateTime $onDateTime = null)
    {
        /** @var $claimsForAccountQuery ClaimsForAccountQuery */
        $claimsForAccountQuery = $this->get(ClaimsForAccountQuery::SERVICE_NAME);

        $claimsForAccountQuery->setAccountId($accountId);
        $claimsForAccountQuery->setOnDateTime($onDateTime);

        return $claimsForAccountQuery->run();
    }

    /**
     * Return the claim with given id
     *
     * @param $claimId
     * @param \DateTime $onDateTime if left null, the current date and time is used
     *
     * @return mixed
     */
    public function getClaimForId($claimId, \DateTime $onDateTime = null)
    {
        /** @var $claimForIdQuery ClaimForIdQuery */
        $claimForIdQuery = $this->get(ClaimForIdQuery::SERVICE_NAME);

        $claimForIdQuery->setClaimId($claimId);
        $claimForIdQuery->setOnDateTime($onDateTime);

        return $claimForIdQuery->run();
    }

    /**
     * Return all characters that are not claimed at the given date and time.
     * Returns an empty array when no such characters exist.
     *
     * @param \DateTime $onDateTime if left null, the current date and time is used
     *
     * @return mixed
     */
    public function getUnclaimedCharacters(\DateTime $onDateTime = null)
    {
        /** @var $unclaimedCharactersQuery UnclaimedCharactersQuery */
        $unclaimedCharactersQuery = $this->get(UnclaimedCharactersQuery::SERVICE_NAME);

        $unclaimedCharactersQuery->setOnDateTime($onDateTime);

        return $unclaimedCharactersQuery->run();
    }

    /**
     * Create a new claim for the given $characterId with the values supplied
     *
     * @param int $accountId
     * @param int $characterId
     * @param bool $playsTank
     * @param bool $playsHealer
     * @param bool $playsDPS
     */
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

    /**
     * Update the existing claim with id $claimId
     *
     * @param int $claimId
     * @param bool $playsTank
     * @param bool $playsHealer
     * @param bool $playsDPS
     */
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
     * End the claim with the given $claimId
     *
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
     * Create a new character with the given name, level, race and class.
     *
     * @param string $name
     * @param int $level
     * @param GameRace $gameRace
     * @param GameClass $gameClass
     * @param string $guild
     * @param string $realm
     */
    public function createCharacter($name, $level, GameRace $gameRace, GameClass $gameClass, $guild, $realm)
    {
        /** @var $createCharacterCommand CreateCharacterCommand */
        $createCharacterCommand = $this->get(CreateCharacterCommand::SERVICE_NAME);

        $createCharacterCommand->setName($name);
        $createCharacterCommand->setLevel($level);
        $createCharacterCommand->setGameRace($gameRace);
        $createCharacterCommand->setGameClass($gameClass);
        $createCharacterCommand->setRealm($realm);
        $createCharacterCommand->setGuild($guild);

        $createCharacterCommand->run();
    }

    /**
     * Update an existing character with $characterId with the given name, level, race and class.
     *
     * @param int $characterId
     * @param string $name
     * @param int $level
     * @param GameRace $gameRace
     * @param GameClass $gameClass
     * @param string $guild
     */
    public function updateCharacter($characterId, $name, $level, GameRace $gameRace, GameClass $gameClass, $guild)
    {
        /** @var $updateCharacterCommand UpdateCharacterCommand */
        $updateCharacterCommand = $this->get(UpdateCharacterCommand::SERVICE_NAME);

        $updateCharacterCommand->setCharacterId($characterId);
        $updateCharacterCommand->setName($name);
        $updateCharacterCommand->setLevel($level);
        $updateCharacterCommand->setGameRace($gameRace);
        $updateCharacterCommand->setGameClass($gameClass);
        $updateCharacterCommand->setGuild($guild);

        $updateCharacterCommand->run();
    }

    /**
     * End the given character with $characterId
     *
     * @param int $characterId
     */
    public function endCharacter($characterId)
    {
        /** @var $endCharacterCommand EndCharacterCommand */
        $endCharacterCommand = $this->get(EndCharacterCommand::SERVICE_NAME);

        $endCharacterCommand->setCharacterId($characterId);

        $endCharacterCommand->run();
    }
}