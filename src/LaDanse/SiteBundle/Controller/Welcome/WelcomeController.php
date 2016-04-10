<?php

namespace LaDanse\SiteBundle\Controller\Welcome;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\SiteBundle\Common\LaDanseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Response;

class WelcomeController extends LaDanseController
{
	/**
     * @return Response
     *
     * @Route("/", name="welcomeIndex")
     */
    public function indexAction()
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        if (!$authContext->isAuthenticated())
        {
            return $this->render("LaDanseSiteBundle::welcome.html.twig");
        }
        else
        {
            return $this->redirect($this->generateUrl('menuIndex'));
        }
    }
}
