<?php

namespace LaDanse\SiteBundle\Controller\Settings;

use LaDanse\CommonBundle\Helper\LaDanseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

use JMS\DiExtraBundle\Annotation as DI;

class SettingsIndexController extends LaDanseController
{
	/**
	 * @var $logger \Monolog\Logger
	 * @DI\Inject("monolog.logger.latte")
	 */
	private $logger;

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

        return $this->redirect($this->generateUrl('editProfile'));
    }
}
