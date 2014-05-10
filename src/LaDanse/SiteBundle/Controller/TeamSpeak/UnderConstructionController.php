<?php

namespace LaDanse\SiteBundle\Controller\TeamSpeak;

use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use LaDanse\CommonBundle\Helper\LaDanseController;

class UnderConstructionController extends LaDanseController
{
	/**
     * @Route("/", name="teamSpeakUnderConstruction")
     * @Template("LaDanseSiteBundle:teamspeak:underConstruction.html.twig")
     */
    public function indexAction(Request $request)
    {
    }
}
