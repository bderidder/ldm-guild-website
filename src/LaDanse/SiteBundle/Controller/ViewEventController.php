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
 * @Route("/event/{id}")
*/
class ViewEventController extends LaDanseController
{
	const EVENT_REPOSITORY = 'LaDanseDomainBundle:Event';

	/**
     * @Route("/view", name="viewEventIndex")
     */
    public function indexAction(Request $request, $id)
    {
        $authContext = new AuthenticationContext($this->get('LaDanse.ContainerInjector'), $request);

    	$em = $this->getDoctrine();
    	$repository = $em->getRepository(self::EVENT_REPOSITORY);
    	$event = $repository->find($id);

        if (null === $event)
        {
            return $this->redirect($this->generateUrl('viewEventsIndex'));
        }
        else
        {
            return $this->render('LaDanseSiteBundle::viewEvent.html.twig',
                array('event' => $event, 'auth' => $authContext)
            );
        }
    }

    /**
     * @Route("/delete", name="deleteEventIndex")
     */
    public function deleteAction(Request $request, $id)
    {
    	$em = $this->getDoctrine()->getManager();
    	$repository = $this->getDoctrine()->getRepository(self::EVENT_REPOSITORY);

    	$event = $repository->find($id);

        if (null === $event)
        {
            return $this->redirect($this->generateUrl('viewEventsIndex'));
        }
        else
        {
    	   $em->remove($event);

    	   $em->flush();

    	   return $this->redirect($this->generateUrl('viewEventsIndex'));
        }
    }
}
