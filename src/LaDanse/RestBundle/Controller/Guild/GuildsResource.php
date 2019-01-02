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
use LaDanse\ServicesBundle\Service\DTO\GameData\PatchGuild;
use LaDanse\ServicesBundle\Service\GameData\GameDataService;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GuildsResource extends AbstractRestController
{
    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Route("/", name="getAllGuilds", options = { "expose" = true }, methods={"GET"})
     */
    public function getAllGuildsAction(Request $request)
    {
        /** @var GameDataService $characterService */
        $gameDataService = $this->get(GameDataService::SERVICE_NAME);

        $guildsDto = $gameDataService->getAllGuilds();

        return new JsonResponse(ResourceHelper::array($guildsDto));
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/", name="postGuild", methods={"POST"})
     */
    public function postGuildAction(Request $request)
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
