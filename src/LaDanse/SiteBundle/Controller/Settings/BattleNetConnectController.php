<?php

namespace LaDanse\SiteBundle\Controller\Settings;

use LaDanse\CommonBundle\Helper\LaDanseController;
use LaDanse\ServicesBundle\Service\Settings\SettingsService;
use LaDanse\SiteBundle\Form\Model\CalExportFormModel;
use LaDanse\SiteBundle\Form\Type\CalExportFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;

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

        /** @var SettingsService $settingsService */
        $settingsService = $this->get(SettingsService::SERVICE_NAME);

        return $this->render('LaDanseSiteBundle:settings:battleNetConnect.html.twig');
    }
}
