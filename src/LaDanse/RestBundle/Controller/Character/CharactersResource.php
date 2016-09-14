<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\RestBundle\Controller\Character;

use JMS\Serializer\SerializerBuilder;
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
     * @Route("/{characterId}/claim", name="postClaim")
     * @Method({"POST"})
     */
    public function postClaimAction(Request $request, $characterId)
    {
        $accountId = $this->getAccount()->getId();

        $serializer = SerializerBuilder::create()->build();

        try
        {
            $patchClaim = $serializer->deserialize(
                $request->getContent(),
                PatchClaim::class,
                'json'
            );

            $validator = $this->get('validator');
            $errors = $validator->validate($patchClaim);

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

        /** @var CharacterService $characterService */
        $characterService = $this->get(CharacterService::SERVICE_NAME);

        try
        {
            $characterDto = $characterService->postClaim($characterId, $accountId, $patchClaim);

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
}
