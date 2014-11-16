<?php

namespace LaDanse\SiteBundle\Controller;

use LaDanse\CommonBundle\Helper\LaDanseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/")
*/
class WelcomeController extends LaDanseController
{
	/**
     * @return Response
     *
     * @Route("/", name="welcomeIndex")
     */
    public function indexAction()
    {
        return $this->render("LaDanseSiteBundle::welcome.html.twig");
    }
}
