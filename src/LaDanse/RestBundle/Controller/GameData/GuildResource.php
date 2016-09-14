<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\RestBundle\Controller\GameData;

use LaDanse\RestBundle\Common\AbstractRestController;
use LaDanse\RestBundle\Common\JsonResponse;
use LaDanse\RestBundle\Common\ResourceHelper;
use LaDanse\ServicesBundle\Common\ServiceException;
use LaDanse\ServicesBundle\Service\DTO\GameData\PatchGuild;
use LaDanse\ServicesBundle\Service\GameData\GameDataService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/guilds")
 */
class GuildResource extends AbstractRestController
{
    /**
     * @return Response
     *
     * @Route("", name="getAllGuilds")
     * @Method({"GET"})
     */
    public function getAllGuilds()
    {
        /** @var GameDataService $gameDataService */
        $gameDataService = $this->get(GameDataService::SERVICE_NAME);

        $guilds = $gameDataService->getAllGuilds();

        return new JsonResponse($guilds);
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

            return new JsonResponse($dtoGuild);
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
