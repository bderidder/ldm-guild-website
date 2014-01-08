<?php

namespace LaDanse\SiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use LaDanse\CommonBundle\Helper\LaDanseController;

/**
 * @Route("/")
*/
class WelcomeController extends LaDanseController
{
	/**
     * @Route("/", name="welcomeIndex")
     * @Template("LaDanseSiteBundle::welcome.html.twig")
     */
    public function indexAction(Request $request)
    {
    }
}
