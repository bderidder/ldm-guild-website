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
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/")
 */
class CharactersClaimedByAccountQuery extends AbstractRestController
{
    /**
     * @ApiDoc(
     *  description="Find all characters claimed by the given account"
     * )
     *
     * @param Request $request
     *
     * @return Response
     *
     * @Route("/charactersClaimedByAccount", name="getCharactersClaimedByAccount", options = { "expose" = true })
     * @Method({"GET"})
     */
    public function getCharactersClaimedByAccountAction(Request $request)
    {
        $accountId = $request->query->get('accountId');

        if ($accountId == null || ($accountId != (int) $accountId))
        {
            return ResourceHelper::createErrorResponse(
                $request,
                400,
                "accountId not found in query parameters or has invalid value"
            );
        }

        /** @var CharacterService $characterService */
        $characterService = $this->get(CharacterService::SERVICE_NAME);

        try
        {
            $characterDtos = $characterService->getCharactersClaimedByAccount($accountId);

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
