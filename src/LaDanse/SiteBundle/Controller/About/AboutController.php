<?php

namespace LaDanse\SiteBundle\Controller\About;

use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use LaDanse\CommonBundle\Helper\LaDanseController;

class AboutController extends LaDanseController
{
	/**
     * @Route("/", name="aboutIndex")
     * @Template("LaDanseSiteBundle:about:about.html.twig")
     */
    public function indexAction(Request $request)
    {
    }
}
