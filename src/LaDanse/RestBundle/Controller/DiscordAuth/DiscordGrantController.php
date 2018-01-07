<?php

namespace LaDanse\RestBundle\Controller\DiscordAuth;


use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\RestBundle\Common\AbstractRestController;
use LaDanse\RestBundle\Common\JsonResponse;
use LaDanse\RestBundle\Common\ResourceHelper;
use LaDanse\ServicesBundle\Common\ServiceException;
use LaDanse\ServicesBundle\Service\Discord\DiscordConnectService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/")
 */
class DiscordGrantController extends AbstractRestController
{
    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    private $logger;

    /**
     * @var $logger \Doctrine\Bundle\DoctrineBundle\Registry
     * @DI\Inject("doctrine")
     */
    private $doctrine;

    /**
     * @var EventDispatcherInterface $eventDispatcher
     * @DI\Inject("event_dispatcher")
     */
    private $eventDispatcher;

    /**
     * @var DiscordConnectService $discordConnectService
     * @DI\Inject(DiscordConnectService::SERVICE_NAME)
     */
    private $discordConnectService;

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Route("/grant", name="discordGrant", options = { "expose" = true })
     * @Method({"GET","HEAD"})
     */
    public function discordGrantAction(Request $request)
    {
        try
        {
            $authCode = $request->query->get('authCode');

            $accessToken = $this->discordConnectService->grantAccessTokenRequest($authCode);

            return new JsonResponse($accessToken);
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
