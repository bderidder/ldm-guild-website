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
use LaDanse\ServicesBundle\Service\GuildCharacter\CharacterService;
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
        /** @var CharacterService $characterService */
        $characterService = $this->get(CharacterService::SERVICE_NAME);

        $characters = $characterService->getAllCharactersInGuild(
            new StringReference($guildId)
        );

        return new JsonResponse($characters);
    }
}
