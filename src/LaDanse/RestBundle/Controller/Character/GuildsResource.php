<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\RestBundle\Controller\Character;

use LaDanse\RestBundle\Common\AbstractRestController;
use LaDanse\RestBundle\Common\JsonResponse;
use LaDanse\RestBundle\Common\ResourceHelper;
use LaDanse\ServicesBundle\Service\Character\CharacterService;
use LaDanse\ServicesBundle\Service\DTO\Reference\StringReference;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/guilds")
 */
class GuildsResource extends AbstractRestController
{
    /**
     * @param Request $request
     * @param $guildId
     *
     * @return Response
     *
     * @Route("/{guildId}", name="getAllCharactersInGuild")
     * @Method({"GET"})
     */
    public function getAllCharactersInGuild(Request $request, $guildId)
    {
        /** @var CharacterService $characterService */
        $characterService = $this->get(CharacterService::SERVICE_NAME);

        $characters = $characterService->getAllCharactersInGuild(
            new StringReference($guildId)
        );

        return new JsonResponse(ResourceHelper::array($characters));
    }
}
