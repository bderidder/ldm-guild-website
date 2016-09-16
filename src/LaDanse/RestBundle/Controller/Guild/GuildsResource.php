<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\RestBundle\Controller\Guild;

use LaDanse\RestBundle\Common\AbstractRestController;
use LaDanse\RestBundle\Common\JsonResponse;
use LaDanse\RestBundle\Common\ResourceHelper;
use LaDanse\ServicesBundle\Common\ServiceException;
use LaDanse\ServicesBundle\Service\Character\CharacterService;
use LaDanse\ServicesBundle\Service\DTO\GameData\PatchGuild;
use LaDanse\ServicesBundle\Service\DTO\Reference\StringReference;
use LaDanse\ServicesBundle\Service\GameData\GameDataService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/")
 */
class GuildsResource extends AbstractRestController
{
    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Route("", name="getAllGuilds")
     * @Method({"GET"})
     */
    public function getAllGuilds(Request $request)
    {
        /** @var GameDataService $characterService */
        $gameDataService = $this->get(GameDataService::SERVICE_NAME);

        $guildsDto = $gameDataService->getAllGuilds();

        return new JsonResponse(ResourceHelper::array($guildsDto));
    }

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

    /**
     * @param Request $request
     * @return Response
     * @Route("", name="postGuild")
     * @Method({"POST"})
     */
    public function postGuild(Request $request)
    {
        /** @var GameDataService $gameDataService */
        $gameDataService = $this->get(GameDataService::SERVICE_NAME);

        try
        {
            $patchGuild = $this->getDtoFromContent($request, PatchGuild::class);

            $dtoGuild = $gameDataService->postGuild($patchGuild);

            return new JsonResponse(ResourceHelper::object($dtoGuild));
        }
        catch(ServiceException $serviceException)
        {
            return ResourceHelper::createErrorResponse(
                $request,
                $serviceException->getCode(),
                $serviceException->getMessage()
            );
        }
    }
}
