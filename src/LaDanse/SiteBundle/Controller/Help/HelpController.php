<?php

namespace LaDanse\SiteBundle\Controller\Help;

use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use LaDanse\CommonBundle\Helper\LaDanseController;

class HelpController extends LaDanseController
{
	/**
     * @Route("/", name="helpIndex")
     * @Template("LaDanseSiteBundle:help:index.html.twig")
     */
    public function indexAction(Request $request)
    {
    }
}
