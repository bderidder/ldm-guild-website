<?php

namespace LaDanse\SiteBundle\Controller\DiscordAuth;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Service\Discord\DiscordConnectService;
use LaDanse\ServicesBundle\Service\Discord\DiscordRedirectValidator;
use LaDanse\SiteBundle\Common\LaDanseController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DiscordAuthController extends LaDanseController
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
     * @param Request $request
     *
     * @return Response
     *
     * @Route("/inform", name="informDiscordAuth")
     */
    public function informDiscordAuthAction(Request $request)
    {
        $nonce = $request->query->get('nonce');
        $redirect = $request->query->get('redirect');

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::AUTHZ_DISCORD_INFORM,
                $this->getAuthenticationService()->getCurrentContext()->getAccount())
        );

        return $this->render(
            'LaDanseSiteBundle:discord:inform.html.twig',
            [
                "nonce" => $nonce,
                "redirectUrl" => $redirect
            ]
        );
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Route("/start", name="startDiscordAuth")
     */
    public function startDiscordAuthAction(Request $request)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();
        $account = $authContext->getAccount();

        $nonce = $request->query->get('nonce');
        $redirect = $request->query->get('redirect');

        $authCode = $this->discordConnectService->authCodeRequest($account->getId(), $nonce);

        if (DiscordRedirectValidator::validate($redirect))
        {
            return $this->redirect($redirect . "?authCode=" . $authCode);
        }

        return $this->redirectToRoute('discordConnect');
    }
}
