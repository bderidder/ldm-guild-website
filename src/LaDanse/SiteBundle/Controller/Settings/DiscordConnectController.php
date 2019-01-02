<?php

namespace LaDanse\SiteBundle\Controller\Settings;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Service\Discord\DiscordConnectService;
use LaDanse\ServicesBundle\Service\SocialConnect\SocialConnectService;
use LaDanse\ServicesBundle\Service\SocialConnect\VerificationReport;
use LaDanse\SiteBundle\Common\LaDanseController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DiscordConnectController extends LaDanseController
{
    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    private $logger;

    /**
     * @var $eventDispatcher EventDispatcherInterface
     * @DI\Inject("event_dispatcher")
     */
    private $eventDispatcher;

    /**
     * @var DiscordConnectService $discordConnectService
     * @DI\Inject(DiscordConnectService::SERVICE_NAME)
     */
    private $discordConnectService;

	/**
     * @param $request Request
     *
     * @return Response
     *
     * @Route("/discordConnect", name="discordConnect")
     */
    public function discordConnectAction(Request $request)
    {
    	$authContext = $this->getAuthenticationService()->getCurrentContext();

    	if (!$authContext->isAuthenticated())
    	{
            $this->logger->warning(__CLASS__ . ' the user was not authenticated in battleNetConnect');

    		return $this->redirect($this->generateUrl('welcomeIndex'));
    	}

        $isConnected = $this->discordConnectService->getDiscordConnectStatus(
            $authContext->getAccount()->getId())->isConnected();

        return $this->render(
            'LaDanseSiteBundle:settings:discordConnect.html.twig',
            [
                "isConnected" => $isConnected
            ]);
    }

    /**
     * @param $request Request
     *
     * @return Response
     *
     * @Route("/disconnectDiscord", name="disconnectDiscord")
     */
    public function disconnectDiscordAction(Request $request)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        $account = $authContext->getAccount();

        $this->discordConnectService->disconnectDiscord($account->getId());

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::AUTHZ_DISCORD_DISCONNECT,
                $this->getAuthenticationService()->getCurrentContext()->getAccount())
        );

        return $this->redirectToRoute('discordConnect');
    }
}
