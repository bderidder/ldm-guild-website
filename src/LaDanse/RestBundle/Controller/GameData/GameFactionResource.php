<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\RestBundle\Controller\GameData;

use LaDanse\RestBundle\Common\AbstractRestController;
use LaDanse\RestBundle\Common\JsonResponse;
use LaDanse\ServicesBundle\Service\GameData\GameDataService;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/gameFactions")
 */
class GameFactionResource extends AbstractRestController
{
    /**
     * @ApiDoc(
     *  description="Get all known game factions"
     * )
     *
     * @return Response
     *
     * @Route("/", name="getAllGameFactions", options = { "expose" = true })
     * @Method({"GET"})
     */
    public function getAllGameFactionsAction()
    {
        /** @var GameDataService $gameDataService */
        $gameDataService = $this->get(GameDataService::SERVICE_NAME);

        $gameFactions = $gameDataService->getAllGameFactions();

        return new JsonResponse($gameFactions);
    }
}
