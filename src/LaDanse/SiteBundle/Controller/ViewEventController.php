<?php

namespace LaDanse\SiteBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use LaDanse\CommonBundle\Helper\LaDanseController;

/**
 * @Route("/Event/{id}")
*/
class ViewEventController extends LaDanseController
{
	/**
     * @Route("/", name="viewEventIndex")
     * @Template("LaDanseSiteBundle::viewEvent.html.twig")
     */
    public function indexAction(Request $request, $id)
    {
    	$event = $this->getDoctrine()->getRepository('LaDanseDomainBundle:Event')->find($id);

    	return array('event' => $event);
    }
}
