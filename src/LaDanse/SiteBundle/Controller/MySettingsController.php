<?php

namespace LaDanse\SiteBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use LaDanse\CommonBundle\Helper\LaDanseController;

use LaDanse\SiteBundle\Security\AuthenticationContext;

/**
 * @Route("/mysettings")
*/
class MySettingsController extends LaDanseController
{
	/**
     * @Route("/", name="mySettingsIndex")
     * @Template("LaDanseSiteBundle::mySettings.html.twig")
     */
    public function indexAction(Request $request)
    {
    	$authContext = $this->getAuthenticationService()->getCurrentContext();

    	if (!$authContext->isAuthenticated())
    	{
    		return $this->redirect($this->generateUrl('welcomeIndex'));
    	}
    }
}
