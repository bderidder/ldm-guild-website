<?php

namespace LaDanse\SiteBundle\Controller\Claims;

use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use LaDanse\CommonBundle\Helper\LaDanseController;

class UnderConstructionController extends LaDanseController
{
	/**
     * @Route("/underconstruction", name="claimsUnderConstruction")
     * @Template("LaDanseSiteBundle:claims:underConstruction.html.twig")
     */
    public function indexAction(Request $request)
    {
    }
}
