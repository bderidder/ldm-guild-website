<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\RestBundle\Controller\Character;

use LaDanse\RestBundle\Common\AbstractRestController;
use LaDanse\RestBundle\Common\JsonResponse;
use LaDanse\ServicesBundle\Service\DTO\Reference\StringReference;
use LaDanse\ServicesBundle\Service\GameData\GameDataService;
use LaDanse\ServicesBundle\Service\GuildCharacter\GuildCharacterService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/guilds")
 */
class CharacterResource extends AbstractRestController
{
    /**
     * @param Request $request
     * @param $guildId
     *
     * @return Response
     *
     * @Route("/{guildId}", name="getAllCharacters")
     * @Method({"GET"})
     */
    public function getAllCharacters(Request $request, $guildId)
    {
        /** @var GuildCharacterService $characterService */
        $characterService = $this->get(GuildCharacterService::SERVICE_NAME);

        $characters = $characterService->newGetAllGuildCharacters(
            new StringReference($guildId)
        );

        return new JsonResponse($characters);
    }
}
