<?php

namespace LaDanse\SiteBundle\Controller\Gallery;

use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use LaDanse\CommonBundle\Helper\LaDanseController;

class UnderConstructionController extends LaDanseController
{
	/**
     * @Route("/", name="galleryUnderConstruction")
     * @Template("LaDanseSiteBundle:gallery:underConstruction.html.twig")
     */
    public function indexAction(Request $request)
    {
    }
}
