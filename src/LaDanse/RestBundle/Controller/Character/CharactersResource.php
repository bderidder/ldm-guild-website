<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\RestBundle\Controller\Character;

use LaDanse\RestBundle\Common\AbstractRestController;
use LaDanse\RestBundle\Common\JsonResponse;
use LaDanse\RestBundle\Common\ResourceHelper;
use LaDanse\ServicesBundle\Common\ServiceException;
use LaDanse\ServicesBundle\Service\Character\CharacterService;
use LaDanse\ServicesBundle\Service\DTO\Character\PatchClaim;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/characters")
 */
class CharactersResource extends AbstractRestController
{
    /**
     * @param Request $request
     * @param $characterId
     *
     * @return Response
     *
     * @Route("/{characterId}", name="getCharacter")
     * @Method({"GET"})
     */
    public function getCharacter(Request $request, $characterId)
    {
        /** @var CharacterService $characterService */
        $characterService = $this->get(CharacterService::SERVICE_NAME);

        try
        {
            $characterDto = $characterService->getCharacterById($characterId);

            return new JsonResponse(ResourceHelper::array($characterDto));
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

    /**
     * @param Request $request
     * @param $characterId
     *
     * @return Response
     *
     * @Route("/{characterId}/claim", name="postClaim")
     * @Method({"POST"})
     */
    public function postClaimAction(Request $request, $characterId)
    {
        try
        {
            $accountId = $this->getAccount()->getId();

            $patchClaim = $this->getDtoFromContent($request, PatchClaim::class);

            /** @var CharacterService $characterService */
            $characterService = $this->get(CharacterService::SERVICE_NAME);

            $characterDto = $characterService->postClaim($characterId, $accountId, $patchClaim);

            return new JsonResponse(ResourceHelper::object($characterDto));
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

    /**
     * @param Request $request
     * @param $characterId
     *
     * @return Response
     *
     * @Route("/{characterId}/claim", name="putClaimAction")
     * @Method({"PUT"})
     */
    public function putClaimAction(Request $request, $characterId)
    {
        try
        {
            $patchClaim = $this->getDtoFromContent($request, PatchClaim::class);

            /** @var CharacterService $characterService */
            $characterService = $this->get(CharacterService::SERVICE_NAME);

            $characterDto = $characterService->putClaim($characterId, $patchClaim);

            return new JsonResponse(ResourceHelper::object($characterDto));
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

    /**
     * @param Request $request
     * @param $characterId
     *
     * @return Response
     *
     * @Route("/{characterId}/claim", name="deleteClaimAction")
     * @Method({"DELETE"})
     */
    public function deleteClaimAction(Request $request, $characterId)
    {
        try
        {
            /** @var CharacterService $characterService */
            $characterService = $this->get(CharacterService::SERVICE_NAME);

            $characterDto = $characterService->deleteClaim($characterId);

            return new JsonResponse(ResourceHelper::object($characterDto));
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
