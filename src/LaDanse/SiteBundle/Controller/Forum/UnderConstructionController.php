<?php

namespace LaDanse\SiteBundle\Controller\Forum;

use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use LaDanse\CommonBundle\Helper\LaDanseController;

class UnderConstructionController extends LaDanseController
{
	/**
     * @Route("/", name="forumUnderConstruction")
     * @Template("LaDanseSiteBundle:forum:underConstruction.html.twig")
     */
    public function indexAction(Request $request)
    {
    }
}
