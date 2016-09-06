<?php

namespace LaDanse\ServicesBundle\Service\GuildCharacter;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\ServicesBundle\Common\LaDanseService;
use LaDanse\ServicesBundle\Service\DTO\Character\Character;
use LaDanse\ServicesBundle\Service\DTO\Character\PatchCharacter;
use LaDanse\ServicesBundle\Service\DTO\Character\PatchClaim;
use LaDanse\ServicesBundle\Service\DTO\Reference\StringReference;
use LaDanse\ServicesBundle\Service\GuildCharacter\Command\CharacterSessionImpl;
use LaDanse\ServicesBundle\Service\GuildCharacter\Command\CreateGuildSyncSessionCommand;
use LaDanse\ServicesBundle\Service\GuildCharacter\Query\GetAllCharactersInGuildQuery;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @DI\Service(CharacterService::SERVICE_NAME, public=true)
 */
class CharacterService extends LaDanseService
{
    const SERVICE_NAME = 'LaDanse.CharacterService';

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
     * Returns an array of all characters who were in the guild on $onDateTime
     * Properties are taken as valid on $onDateTime
     *
     * @param StringReference $guildReference
     * @param \DateTime $onDateTime if left null, the current date and time is used
     *
     * @return array
     */
    public function getAllCharactersInGuild(StringReference $guildReference, \DateTime $onDateTime = null) : array
    {
        /** @var GetAllCharactersInGuildQuery $query */
        $query = $this->get(GetAllCharactersInGuildQuery::SERVICE_NAME);

        if ($onDateTime == null)
        {
            $onDateTime = new \DateTime();
        }

        $query->setGuildReference($guildReference);
        $query->setOnDateTime($onDateTime);

        return $query->run();
    }

    /**
     * @param int $accountId
     *
     * @return array
     */
    public function getAllCharactersClaimedByAccount(integer $accountId) : array
    {
    }

    /**
     * @param array $keywords
     * @param \DateTime|null $onDateTime
     *
     * @return array
     */
    public function getCharactersByKeywords(array $keywords, \DateTime $onDateTime = null) : array
    {
    }

    /**
     * @param CharacterSession $characterSession
     * @param PatchCharacter $patchCharacter
     *
     * @return Character
     */
    public function postCharacter(
        CharacterSession $characterSession,
        PatchCharacter $patchCharacter) : Character
    {
    }

    /**
     * @param CharacterSession $characterSession
     * @param int $characterId
     * @param PatchCharacter $patchCharacter
     *
     * @return Character
     */
    public function patchCharacter(
        CharacterSession $characterSession,
        integer $characterId,
        PatchCharacter $patchCharacter) : Character
    {
    }

    /**
     * @param CharacterSession $characterSession
     * @param int $characterId
     */
    public function deleteCharacter(CharacterSession $characterSession, integer $characterId)
    {
    }

    /**
     * @param int $characterId
     * @param PatchClaim $patchClaim
     *
     * @return Character
     */
    public function postClaim(integer $characterId, PatchClaim $patchClaim) : Character
    {
    }

    /**
     * @param integer $characterId
     * @param PatchClaim $patchClaim
     *
     * @return Character
     */
    public function putClaim(integer $characterId, PatchClaim $patchClaim) : Character
    {
    }

    /**
     * @param integer $characterId
     *
     * @return Character
     */
    public function deleteClaim(integer $characterId): Character
    {
    }

    /**
     * @param StringReference $guildId
     *
     * @return CharacterSession
     */
    public function createGuildSyncSession(StringReference $guildId) : CharacterSession
    {
        /** @var CreateGuildSyncSessionCommand $cmd */
        $cmd = $this->get(CreateGuildSyncSessionCommand::SERVICE_NAME);

        $cmd->setGuildId($guildId);

        return $cmd->run();
    }

    /**
     * @param integer $accountId
     *
     * @return CharacterSession
     *
     * @throws \Exception
     */
    public function createWoWProfileSyncSession(integer $accountId) : CharacterSession
    {
        throw new \Exception("This method is not yet implemented");
    }

    /**
     * @param CharacterSession $characterSession
     *
     * @return CharacterService
     *
     * @throws \Exception
     */
    public function endCharacterSession(CharacterSession $characterSession) : CharacterService
    {
        if (!($characterSession instanceof CharacterSessionImpl))
        {
            throw new \Exception("Unknown implementation for CharacterSession");
        }

        /** @var CharacterSessionImpl $characterSessionImpl */
        $characterSessionImpl = $characterSession;

        $characterSessionImpl->endSession();

        return $this;
    }
}