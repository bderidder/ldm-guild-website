<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\RestBundle\Controller\GameData;

use LaDanse\RestBundle\Common\AbstractRestController;
use LaDanse\RestBundle\Common\JsonResponse;
use LaDanse\ServicesBundle\Service\GameData\GameDataService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/gameRaces")
 */
class GameRaceResource extends AbstractRestController
{
    /**
     * @return Response
     *
     * @Route("", name="getAllGameRaces")
     * @Method({"GET"})
     */
    public function getAllGuilds()
    {
        /** @var GameDataService $gameDataService */
        $gameDataService = $this->get(GameDataService::SERVICE_NAME);

        $gameRaces = $gameDataService->getAllGameRaces();

        return new JsonResponse($gameRaces);
    }
}
