<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Service\GameData;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity as Entity;
use LaDanse\ServicesBundle\Service\DTO as DTO;
use LaDanse\ServicesBundle\Common\LaDanseService;
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
     * @return Entity\GameData\GameRace[]
     */
    public function getAllRaces()
    {
        $em = $this->getDoctrine()->getManager();

        return $em->getRepository(Entity\GameData\GameRace::REPOSITORY)->findAll();
    }

    /**
     * @return Entity\GameData\GameClass[]
     */
    public function getAllClasses()
    {
        $em = $this->getDoctrine()->getManager();

        return $em->getRepository(Entity\GameData\GameClass::REPOSITORY)->findAll();
    }

    public function getAllGuilds() : array
    {

    }

    public function createGuild(DTO\GameData\PatchGuild $patchGuild) : DTO\GameData\Guild
    {

    }

    public function updateGuild(string $guildId, DTO\GameData\PatchGuild $patchGuild)
    {

    }

    public function removeGuild(string $guildId)
    {

    }

    public function getAllRealms() : array
    {

    }

    public function createRealm(DTO\GameData\PatchRealm $patchRealm) : DTO\GameData\Realm
    {

    }

    public function updateRealm(string $realmId, DTO\GameData\PatchRealm $patchRealm)
    {

    }

    public function reamoveRealm(string $realmId)
    {

    }
}