<?php

namespace LaDanse\SiteBundle\Controller\Settings;

use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use LaDanse\CommonBundle\Helper\LaDanseController;

use LaDanse\SiteBundle\Form\Model\SettingsFormModel;
use LaDanse\SiteBundle\Form\Type\SettingsFormType;

class ChangePasswordController extends LaDanseController
{
	/**
     * @Route("/password", name="changePassword")
     * @Template("LaDanseSiteBundle:settings:changePassword.html.twig")
     */
    public function indexAction(Request $request)
    {
    	$authContext = $this->getAuthenticationService()->getCurrentContext();

    	if (!$authContext->isAuthenticated())
    	{
            $this->getLogger()->warn(__CLASS__ . ' the user was not authenticated in indexAction');

    		return $this->redirect($this->generateUrl('welcomeIndex'));
    	}
    }
}
