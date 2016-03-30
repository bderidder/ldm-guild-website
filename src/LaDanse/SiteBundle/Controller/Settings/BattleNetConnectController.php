<?php

namespace LaDanse\SiteBundle\Controller\Settings;

use GuzzleHttp\Exception\ClientException;
use Depotwarehouse\OAuth2\Client\Provider\WowProvider;
use GuzzleHttp\Client;
use LaDanse\CommonBundle\Helper\LaDanseController;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Service\Settings\SettingsService;
use LaDanse\ServicesBundle\Service\SocialConnect\SocialConnectService;
use LaDanse\SiteBundle\Model\BattlenetVerificationModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use JMS\DiExtraBundle\Annotation as DI;

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
            array(
                "isConnected" => $isConnected
            ));
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
        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::BATTLENET_OAUTH_VERIFY,
                $this->getAuthenticationService()->getCurrentContext()->getAccount())
        );

        $authContext = $this->getAuthenticationService()->getCurrentContext();

        $account = $authContext->getAccount();

        /** @var SocialConnectService $socialConnectService */
        $socialConnectService = $this->get(SocialConnectService::SERVICE_NAME);

        $checkTokenUrl = 'https://eu.battle.net/oauth/check_token';
        $charactersUrl = 'https://eu.api.battle.net/wow/user/characters';

        $accessToken = $socialConnectService->getAccessTokenForAccount($account);

        $verificationModel = new BattlenetVerificationModel();

        if ($accessToken == null)
        {
            $verificationModel->setConnected(false);
        }
        else
        {
            $verificationModel->setConnected(true);

            try
            {
                $client = new Client();

                $response = $client->get($checkTokenUrl, array(
                    'query' => array('token' => $accessToken)
                ));

                $this->logger->debug(__CLASS__ . " verifyBattlenetAction check_token success " . $response->getBody());

                $jsonBody = $response->getBody();

                $checkAccessToken = json_decode($jsonBody);

                if (!property_exists($checkAccessToken, 'exp'))
                {
                    $verificationModel->setCheckAccessToken(false);
                }
                else
                {
                    $verificationModel->setCheckAccessToken(true);

                    $expirationDate = new \DateTime();
                    $expirationDate->setTimestamp($checkAccessToken->exp);

                    $verificationModel->setExpirationDate($expirationDate);
                }
            }
            catch(ClientException $e)
            {
                $verificationModel->setCheckAccessToken(false);

                $this->logger->debug(__CLASS__ . " verifyBattlenetAction check_token failure " . $e->getMessage());
            }

            try
            {
                $client = new Client();

                $response = $client->get($charactersUrl, array(
                    'query' => array('access_token' => $accessToken)
                ));

                $this->logger->debug(__CLASS__ . " verifyBattlenetAction characters success " . $response->getBody());

                $jsonBody = $response->getBody();

                $checkAccessToken = json_decode($jsonBody);

                if (!property_exists($checkAccessToken, 'characters'))
                {
                    $verificationModel->setCharactersLoaded(false);
                }
                else
                {
                    $verificationModel->setCharactersLoaded(true);
                }
            }
            catch(ClientException $e)
            {
                $verificationModel->setCharactersLoaded(false);

                $this->logger->debug(__CLASS__ . " verifyBattlenetAction characters failure " . $e->getMessage());
            }
        }

        return $this->render(
            'LaDanseSiteBundle:settings:battleNetVerify.html.twig',
            array(
                "verification" => $verificationModel
            ));
    }
}
