<?php

namespace LaDanse\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/MySettings")
*/
class MySettingsController extends Controller
{
	/**
     * @Route("/", name="mySettingsIndex")
     * @Template("LaDanseSiteBundle::mySettings.html.twig")
     */
    public function indexAction(Request $request)
    {
    }
}
