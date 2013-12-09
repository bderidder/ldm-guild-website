<?php

namespace LaDanse\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/Events")
*/
class ViewEventsController extends Controller
{
	/**
     * @Route("/", name="viewEventsIndex")
     * @Template("LaDanseSiteBundle::viewEvents.html.twig")
     */
    public function indexAction(Request $request)
    {
    }
}
