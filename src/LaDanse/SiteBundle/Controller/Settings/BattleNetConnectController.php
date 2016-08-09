<?php

namespace LaDanse\SiteBundle\Controller\Settings;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Service\SocialConnect\SocialConnectService;
use LaDanse\ServicesBundle\Service\SocialConnect\VerificationReport;
use LaDanse\SiteBundle\Common\LaDanseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BattleNetConnectController extends LaDanseController
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
     * @var SocialConnectService $socialConnectService
     * @DI\Inject(SocialConnectService::SERVICE_NAME)
     */
    private $socialConnectService;

	/**
     * @param $request Request
     *
     * @return Response
     *
     * @Route("/battleNetConnect", name="battleNetConnect")
     */
    public function indexAction(Request $request)
    {
    	$authContext = $this->getAuthenticationService()->getCurrentContext();

    	if (!$authContext->isAuthenticated())
    	{
            $this->logger->warning(__CLASS__ . ' the user was not authenticated in battleNetConnect');

    		return $this->redirect($this->generateUrl('welcomeIndex'));
    	}

        $account = $authContext->getAccount();

        $isConnected = $this->socialConnectService->isAccountConnected($account);

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::BATTLENET_OAUTH_VIEW,
                $this->getAuthenticationService()->getCurrentContext()->getAccount())
        );

        return $this->render(
            'LaDanseSiteBundle:settings:battleNetConnect.html.twig',
            [
                "isConnected" => $isConnected
            ]);
    }

    /**
     * @param $request Request
     *
     * @return Response
     *
     * @Route("/disconnectBattlenet", name="disconnectBattlenet")
     */
    public function disconnectBattlenetAction(Request $request)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        $account = $authContext->getAccount();

        $this->socialConnectService->disconnectAccount($account);

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::BATTLENET_OAUTH_DISCONNECT,
                $this->getAuthenticationService()->getCurrentContext()->getAccount())
        );

        return $this->redirectToRoute('battleNetConnect');
    }

    /**
     * @param $request Request
     *
     * @return Response
     *
     * @Route("/verifyBattlenet", name="verifyBattlenet")
     */
    public function verifyBattlenetAction(Request $request)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        $account = $authContext->getAccount();

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::BATTLENET_OAUTH_VERIFY,
                $account)
        );

        /** @var VerificationReport $verificationReport */
        $verificationReport = $this->socialConnectService->verifyAccountConnection($account);

        return $this->render(
            'LaDanseSiteBundle:settings:battleNetVerify.html.twig',
            [
                "verification" => $verificationReport
            ]);
    }
}
