<?php

namespace LaDanse\SiteBundle\Controller\Menu;

use LaDanse\CommonBundle\Helper\LaDanseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class MenuController extends LaDanseController
{
	/**
     * @return Response
     *
     * @Route("/", name="menuIndex")
     */
    public function indexAction()
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        if (!$authContext->isAuthenticated())
        {
            $this->getLogger()->warning(__CLASS__ . ' the user was not authenticated in menuIndex');

            return $this->redirect($this->generateUrl('welcomeIndex'));
        }

        return $this->render('LaDanseSiteBundle:menu:menu.html.twig');
    }
}
