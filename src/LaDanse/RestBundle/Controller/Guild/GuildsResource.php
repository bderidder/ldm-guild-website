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
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GuildsResource extends AbstractRestController
{
    /**
     * @ApiDoc(
     *  description="Get all guilds known to the system"
     * )
     *
     * @param Request $request
     *
     * @return Response
     *
     * @Route("/", name="getAllGuilds", options = { "expose" = true })
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
     * @ApiDoc(
     *  description="Create a new guild"
     * )
     *
     * @param Request $request
     * @return Response
     * @Route("/", name="postGuild")
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
