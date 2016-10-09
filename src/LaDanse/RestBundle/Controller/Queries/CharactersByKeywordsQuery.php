<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\RestBundle\Controller\Queries;

use LaDanse\RestBundle\Common\AbstractRestController;
use LaDanse\RestBundle\Common\JsonResponse;
use LaDanse\RestBundle\Common\ResourceHelper;
use LaDanse\ServicesBundle\Common\ServiceException;
use LaDanse\ServicesBundle\Service\Character\CharacterService;
use LaDanse\ServicesBundle\Service\DTO\Character\PatchClaim;
use LaDanse\ServicesBundle\Service\DTO\Reference\StringReference;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/")
 */
class CharactersByKeywordsQuery extends AbstractRestController
{
    /**
     * @ApiDoc(
     *  description="Find characters based on, part of, name (the search result is always limited to 20)"
     * )
     *
     * @param Request $request
     *
     * @return Response
     *
     * @Route("/charactersByKeywords", name="getCharactersByKeywords", options = { "expose" = true })
     * @Method({"GET"})
     */
    public function getCharactersByKeywords(Request $request)
    {
        $keywords = $request->query->get('q');

        if ($keywords == null)
        {
            return ResourceHelper::createErrorResponse(
                $request,
                400,
                "q not found in query parameters or has invalid value"
            );
        }

        /** @var CharacterService $characterService */
        $characterService = $this->get(CharacterService::SERVICE_NAME);

        try
        {
            $characterDtos = $characterService->getCharactersByKeywords($keywords);

            return new JsonResponse(ResourceHelper::array($characterDtos));
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
