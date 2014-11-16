<?php

namespace LaDanse\SiteBundle\Controller\TeamSpeak;

use LaDanse\CommonBundle\Helper\LaDanseController;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class TeamSpeakController extends LaDanseController
{
	/**
     * @return Response
     *
     * @Route("/", name="teamSpeakIndex")
     */
    public function indexAction()
    {
    	$authContext = $this->getAuthenticationService()->getCurrentContext();
    	
    	if (!$authContext->isAuthenticated())
        {
            $this->getLogger()->warning(__CLASS__ . ' the user was not authenticated in indexAction');

            return $this->redirect($this->generateUrl('welcomeIndex'));
        }

        return $this->render('LaDanseSiteBundle:teamspeak:index.html.twig');
    }
}
