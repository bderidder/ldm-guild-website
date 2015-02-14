<?php

namespace LaDanse\SiteBundle\Controller\Settings;

use LaDanse\CommonBundle\Helper\LaDanseController;
use LaDanse\ServicesBundle\EventListener\Features;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use LaDanse\ServicesBundle\EventListener\FeatureUseEvent;

use JMS\DiExtraBundle\Annotation as DI;

class SettingsIndexController extends LaDanseController
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
     * @return Response
     *
     * @Route("/", name="welcomeSettings")
     */
    public function indexAction()
    {
    	$authContext = $this->getAuthenticationService()->getCurrentContext();

    	if (!$authContext->isAuthenticated())
    	{
            $this->logger->warning(__CLASS__ . ' the user was not authenticated in indexAction');

    		return $this->redirect($this->generateUrl('welcomeIndex'));
    	}

        $this->eventDispatcher->dispatch(
            FeatureUseEvent::EVENT_NAME,
            new FeatureUseEvent(
                Features::SETTINGS_VIEW,
                $this->getAuthenticationService()->getCurrentContext()->getAccount()
            )
        );

        return $this->redirect($this->generateUrl('editProfile'));
    }
}
