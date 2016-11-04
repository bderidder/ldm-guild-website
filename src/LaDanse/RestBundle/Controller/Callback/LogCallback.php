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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/log")
 */
class LogCallback extends AbstractRestController
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
     * @Route("", name="logCallbackAction", options = { "expose" = true })
     * @Method({"POST"})
     */
    public function logCallbackAction(Request $request)
    {
        try
        {
            $accountId = $this->getAccount()->getId();

            /** @var DTO\Callback\LogCallback $logCallback */
            $logCallback = $this->getDtoFromContent($request, DTO\Callback\LogCallback::class);

            $this->logger->error(
                "AngularJS - " . $logCallback->getSource() . " (accountId " . $accountId . ") - " . $logCallback->getMessage()
            );

            return new Response(null, 200);
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
