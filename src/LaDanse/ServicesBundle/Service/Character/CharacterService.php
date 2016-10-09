<?php

namespace LaDanse\ServicesBundle\Service\Character;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\ServicesBundle\Common\LaDanseService;
use LaDanse\ServicesBundle\Service\Character\Command\DeleteClaimCommand;
use LaDanse\ServicesBundle\Service\Character\Command\PostClaimCommand;
use LaDanse\ServicesBundle\Service\Character\Command\PutClaimCommand;
use LaDanse\ServicesBundle\Service\Character\Query\CharactersByCriteriaQuery;
use LaDanse\ServicesBundle\Service\Character\Query\CharactersByKeywordsQuery;
use LaDanse\ServicesBundle\Service\Character\Query\CharactersClaimedByAccountQuery;
use LaDanse\ServicesBundle\Service\DTO\Character\Character;
use LaDanse\ServicesBundle\Service\DTO\Character\PatchCharacter;
use LaDanse\ServicesBundle\Service\DTO\Character\PatchClaim;
use LaDanse\ServicesBundle\Service\DTO\Character\SearchCriteria;
use LaDanse\ServicesBundle\Service\DTO\Reference\StringReference;
use LaDanse\ServicesBundle\Service\Character\Command\CharacterSessionImpl;
use LaDanse\ServicesBundle\Service\Character\Command\CreateGuildSyncSessionCommand;
use LaDanse\ServicesBundle\Service\Character\Command\PatchCharacterCommand;
use LaDanse\ServicesBundle\Service\Character\Command\TrackCharacterCommand;
use LaDanse\ServicesBundle\Service\Character\Command\UntrackCharacterCommand;
use LaDanse\ServicesBundle\Service\Character\Query\GetAllCharactersInGuildQuery;
use LaDanse\ServicesBundle\Service\Character\Query\GetCharacterByIdQuery;
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
     * @param int $characterId
     * @param \DateTime|null $onDateTime
     *
     * @return Character|null
     */
    public function getCharacterById(int $characterId, \DateTime $onDateTime = null)
    {
        if ($onDateTime == null)
        {
            $onDateTime = new \DateTime();
        }

        /** @var GetCharacterByIdQuery $query */
        $query = $this->get(GetCharacterByIdQuery::SERVICE_NAME);

        $query->setCharacterId($characterId)
              ->setOnDateTime($onDateTime);

        return $query->run();
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
    public function getAllCharactersInGuild(StringReference $guildReference, \DateTime $onDateTime = null)
    {
        if ($onDateTime == null)
        {
            $onDateTime = new \DateTime();
        }

        /** @var GetAllCharactersInGuildQuery $query */
        $query = $this->get(GetAllCharactersInGuildQuery::SERVICE_NAME);

        $query->setGuildReference($guildReference);
        $query->setOnDateTime($onDateTime);

        return $query->run();
    }

    /**
     * @param int $accountId
     * @param \DateTime $onDateTime
     *
     * @return array
     */
    public function getCharactersClaimedByAccount(int $accountId, \DateTime $onDateTime = null) : array
    {
        if ($onDateTime == null)
        {
            $onDateTime = new \DateTime();
        }

        /** @var CharactersClaimedByAccountQuery $query */
        $query = $this->get(CharactersClaimedByAccountQuery::SERVICE_NAME);

        $query->setAccountId($accountId);
        $query->setOnDateTime($onDateTime);

        return $query->run();
    }

    /**
     * @param string $keywords
     * @param \DateTime|null $onDateTime
     *
     * @return array
     */
    public function getCharactersByKeywords(string $keywords, \DateTime $onDateTime = null) : array
    {
        if ($onDateTime == null)
        {
            $onDateTime = new \DateTime();
        }

        /** @var CharactersByKeywordsQuery $query */
        $query = $this->get(CharactersByKeywordsQuery::SERVICE_NAME);

        $query
            ->setOnDateTime($onDateTime)
            ->setKeywords($keywords);

        return $query->run();
    }

    /**
     * @param SearchCriteria $searchCriteria
     * @param \DateTime|null $onDateTime
     *
     * @return array
     */
    public function getCharactersByCriteria(SearchCriteria $searchCriteria, \DateTime $onDateTime = null) : array
    {
        if ($onDateTime == null)
        {
            $onDateTime = new \DateTime();
        }

        /** @var CharactersByCriteriaQuery $query */
        $query = $this->get(CharactersByCriteriaQuery::SERVICE_NAME);

        $query
            ->setOnDateTime($onDateTime)
            ->setSearchCriteria($searchCriteria);

        return $query->run();
    }

    /**
     * @param CharacterSession $characterSession
     * @param PatchCharacter $patchCharacter
     *
     * @return Character
     */
    public function trackCharacter(
        CharacterSession $characterSession,
        PatchCharacter $patchCharacter)
    {
        /** @var TrackCharacterCommand $cmd */
        $cmd = $this->get(TrackCharacterCommand::SERVICE_NAME);

        $cmd->setCharacterSession($characterSession)
            ->setPatchCharacter($patchCharacter);

        return $cmd->run();
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
        int $characterId,
        PatchCharacter $patchCharacter)
    {
        /** @var PatchCharacterCommand $cmd */
        $cmd = $this->get(PatchCharacterCommand::SERVICE_NAME);

        $cmd->setCharacterSession($characterSession)
            ->setCharacterId($characterId)
            ->setPatchCharacter($patchCharacter);

        return $cmd->run();
    }

    /**
     * @param CharacterSession $characterSession
     * @param int $characterId
     */
    public function untrackCharacter(CharacterSession $characterSession, int $characterId)
    {
        /** @var UntrackCharacterCommand $cmd */
        $cmd = $this->get(UntrackCharacterCommand::SERVICE_NAME);

        $cmd->setCharacterSession($characterSession);
        $cmd->setCharacterId($characterId);

        $cmd->run();
    }

    /**
     * @param int $characterId
     * @param int $accountId
     * @param PatchClaim $patchClaim
     *
     * @return Character
     */
    public function postClaim(int $characterId, int $accountId, PatchClaim $patchClaim)
    {
        /** @var PostClaimCommand $cmd */
        $cmd = $this->get(PostClaimCommand::SERVICE_NAME);

        $cmd->setCharacterId($characterId);
        $cmd->setAccountId($accountId);
        $cmd->setPatchClaim($patchClaim);

        return $cmd->run();
    }

    /**
     * @param int $characterId
     * @param PatchClaim $patchClaim
     *
     * @return Character
     */
    public function putClaim(int $characterId, PatchClaim $patchClaim)
    {
        /** @var PutClaimCommand $cmd */
        $cmd = $this->get(PutClaimCommand::SERVICE_NAME);

        $cmd->setCharacterId($characterId);
        $cmd->setPatchClaim($patchClaim);

        return $cmd->run();
    }

    /**
     * @param int $characterId
     *
     * @return Character
     */
    public function deleteClaim(int $characterId): Character
    {
        /** @var DeleteClaimCommand $cmd */
        $cmd = $this->get(DeleteClaimCommand::SERVICE_NAME);

        $cmd->setCharacterId($characterId);

        return $cmd->run();
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
     * @param int $accountId
     *
     * @return CharacterSession
     *
     * @throws \Exception
     */
    public function createWoWProfileSyncSession(int $accountId) : CharacterSession
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