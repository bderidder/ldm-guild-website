<?php

namespace LaDanse\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use LaDanse\SiteBundle\Security\AuthenticationContext;

/**
 * @Route("/Events/Create")
*/
class CreateEventController extends Controller
{
	/**
     * @Route("/", name="createEventIndex")
     * @Template("LaDanseSiteBundle::createEvent.html.twig")
     */
    public function indexAction(Request $request)
    {
		$authContext = new AuthenticationContext($this->get('LaDanse.ContainerInjector'), $request);
    }
}
