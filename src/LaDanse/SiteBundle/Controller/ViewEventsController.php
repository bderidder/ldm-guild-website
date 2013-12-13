<?php

namespace LaDanse\SiteBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use LaDanse\CommonBundle\Helper\LaDanseController;

use LaDanse\SiteBundle\Security\AuthenticationContext;

/**
 * @Route("/events")
*/
class ViewEventsController extends LaDanseController
{
	/**
     * @Route("/", name="viewEventsIndex")
     * @Template("LaDanseSiteBundle::viewEvents.html.twig")
     */
    public function indexAction(Request $request)
    {
    	$authContext = $this->getAuthenticationService()->getCurrentContext();

    	$events = $this->getDoctrine()->getRepository('LaDanseDomainBundle:Event')->findAll();

    	return array('events' => $events);
    }
}
