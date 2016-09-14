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
use LaDanse\ServicesBundle\Service\DTO\GameData\PatchRealm;
use LaDanse\ServicesBundle\Service\GameData\GameDataService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/realms")
 */
class RealmResource extends AbstractRestController
{
    /**
     * @return Response
     *
     * @Route("", name="getAllRealms")
     * @Method({"GET"})
     */
    public function getAllRealms()
    {
        /** @var GameDataService $gameDataService */
        $gameDataService = $this->get(GameDataService::SERVICE_NAME);

        $realms = $gameDataService->getAllRealms();

        return new JsonResponse($realms);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("", name="postRealm")
     * @Method({"POST"})
     */
    public function postRealm(Request $request)
    {
        /** @var GameDataService $gameDataService */
        $gameDataService = $this->get(GameDataService::SERVICE_NAME);

        try
        {
            $patchRealm = $this->getDtoFromContent($request, PatchRealm::class);

            $dtoRealm = $gameDataService->postRealm($patchRealm);

            return new JsonResponse($dtoRealm);
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
