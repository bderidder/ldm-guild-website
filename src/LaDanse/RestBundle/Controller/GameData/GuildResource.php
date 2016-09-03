<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\RestBundle\Controller\GameData;

use JMS\Serializer\SerializerBuilder;
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
        $serializer = SerializerBuilder::create()->build();

        try
        {
            $patchGuild = $serializer->deserialize(
                $request->getContent(),
                PatchGuild::class,
                'json'
            );

            $validator = $this->get('validator');
            $errors = $validator->validate($patchGuild);

            if (count($errors) > 0)
            {
                $errorsString = (string) $errors;

                return ResourceHelper::createErrorResponse(
                    $request,
                    400,
                    $errorsString
                );
            }
        }
        catch(\Exception $exception)
        {
            return ResourceHelper::createErrorResponse(
                $request,
                400,
                $exception->getMessage()
            );
        }

        /** @var GameDataService $gameDataService */
        $gameDataService = $this->get(GameDataService::SERVICE_NAME);

        try
        {
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
