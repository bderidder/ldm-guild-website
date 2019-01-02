<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\RestBundle\Controller\Callback;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\RestBundle\Common\AbstractRestController;
use LaDanse\RestBundle\Common\ResourceHelper;
use LaDanse\ServicesBundle\Service\DTO as DTO;
use LaDanse\ServicesBundle\Common\ServiceException;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/version")
 */
class VersionRequest extends AbstractRestController
{
    /**
     * @var \Psr\Log\LoggerInterface $logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    public $logger;

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Route("", name="currentVersionAction", options = { "expose" = true }, methods={"GET"})
     */
    public function currentVersionAction(Request $request)
    {
        try
        {
            $deploymentVersion = $this->container->getParameter('deployment_version');

            $jsonResponse = (object) [
                "deploymentVersion" => $deploymentVersion
            ];

            return new JsonResponse($jsonResponse);
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
