<?php

namespace LaDanse\SiteBundle\Controller\TeamSpeak;

use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use LaDanse\CommonBundle\Helper\LaDanseController;

class TeamSpeakController extends LaDanseController
{
	/**
     * @Route("/", name="teamSpeakIndex")
     * @Template("LaDanseSiteBundle:teamspeak:index.html.twig")
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
