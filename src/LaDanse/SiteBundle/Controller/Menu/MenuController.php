<?php

namespace LaDanse\SiteBundle\Controller\Menu;

use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use LaDanse\CommonBundle\Helper\LaDanseController;

class MenuController extends LaDanseController
{
	/**
     * @Route("/", name="menuIndex")
     * @Template("LaDanseSiteBundle:menu:menu.html.twig")
     */
    public function indexAction(Request $request)
    {
    }
}
