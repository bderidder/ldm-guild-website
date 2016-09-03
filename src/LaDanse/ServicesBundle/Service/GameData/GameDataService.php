<?php

/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Service\GameData;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\ServicesBundle\Service\DTO as DTO;
use LaDanse\ServicesBundle\Common\LaDanseService;
use LaDanse\ServicesBundle\Service\GameData\Command\PostGuildCommand;
use LaDanse\ServicesBundle\Service\GameData\Command\PostRealmCommand;
use LaDanse\ServicesBundle\Service\GameData\Query\GetAllGameClassesQuery;
use LaDanse\ServicesBundle\Service\GameData\Query\GetAllGameFactionsQuery;
use LaDanse\ServicesBundle\Service\GameData\Query\GetAllGameRacesQuery;
use LaDanse\ServicesBundle\Service\GameData\Query\GetAllGuildsQuery;
use LaDanse\ServicesBundle\Service\GameData\Query\GetAllRealmsQuery;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class GameDataService
 *
 * @DI\Service(GameDataService::SERVICE_NAME, public=true)
 */
class GameDataService extends LaDanseService
{
    const SERVICE_NAME = 'LaDanse.GameDataService';

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
     * @return DTO\GameData\GameRace[]
     */
    public function getAllGameRaces()
    {
        /** @var GetAllGuildsQuery $getAllGameRacesQuery */
        $getAllGameRacesQuery = $this->get(GetAllGameRacesQuery::SERVICE_NAME);

        return $getAllGameRacesQuery->run();
    }

    /**
     * @return DTO\GameData\GameClass[]
     */
    public function getAllGameClasses()
    {
        /** @var GetAllGameClassesQuery $getAllGameClassesQuery */
        $getAllGameClassesQuery = $this->get(GetAllGameClassesQuery::SERVICE_NAME);

        return $getAllGameClassesQuery->run();
    }

    public function getAllGameFactions() : array
    {
        /** @var GetAllGameFactionsQuery $getAllGameFactionsQuery */
        $getAllGameFactionsQuery = $this->get(GetAllGameFactionsQuery::SERVICE_NAME);

        return $getAllGameFactionsQuery->run();
    }

    public function getAllGuilds() : array
    {
        /** @var GetAllGuildsQuery $getAllGuildsQuery */
        $getAllGuildsQuery = $this->get(GetAllGuildsQuery::SERVICE_NAME);

        return $getAllGuildsQuery->run();
    }

    public function postGuild(DTO\GameData\PatchGuild $patchGuild) : DTO\GameData\Guild
    {
        /** @var PostGuildCommand $postGuildCommand */
        $postGuildCommand = $this->get(PostGuildCommand::SERVICE_NAME);

        $postGuildCommand->setPatchGuild($patchGuild);

        return $postGuildCommand->run();
    }

    public function patchGuild(string $guildId, DTO\GameData\PatchGuild $patchGuild)
    {
        throw new \Exception("Not yet implemented");
    }

    public function deleteGuild(string $guildId)
    {
        throw new \Exception("Not yet implemented");
    }

    /**
     * @return DTO\GameData\Realm[]
     */
    public function getAllRealms() : array
    {
        /** @var GetAllRealmsQuery $getAllRealmsQuery */
        $getAllRealmsQuery = $this->get(GetAllRealmsQuery::SERVICE_NAME);

        return $getAllRealmsQuery->run();
    }

    public function postRealm(DTO\GameData\PatchRealm $patchRealm) : DTO\GameData\Realm
    {
        /** @var PostRealmCommand $postRealmCommand */
        $postRealmCommand = $this->get(PostRealmCommand::SERVICE_NAME);

        $postRealmCommand->setPatchRealm($patchRealm);

        return $postRealmCommand->run();
    }

    public function patchRealm(string $realmId, DTO\GameData\PatchRealm $patchRealm)
    {
        throw new \Exception("Not yet implemented");
    }

    public function deleteRealm(string $realmId)
    {
        throw new \Exception("Not yet implemented");
    }
}