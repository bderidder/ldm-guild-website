<?php

namespace LaDanse\SiteBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use LaDanse\CommonBundle\Helper\LaDanseController;

use LaDanse\SiteBundle\Security\AuthenticationContext;

use LaDanse\SiteBundle\Model\EventModel;

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
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        if (!$authContext->isAuthenticated())
        {
            return $this->redirect($this->generateUrl('welcomeIndex'));
        }

    	$em = $this->getDoctrine();
    	$repository = $em->getRepository(self::EVENT_REPOSITORY);
    	$event = $repository->find($id);

        if (null === $event)
        {
            return $this->redirect($this->generateUrl('welcomeIndex'));
        }
        else
        {
            return $this->render('LaDanseSiteBundle::viewEvent.html.twig',
                array('event' => new EventModel($this->getContainerInjector(), $event))
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
            return $this->redirect($this->generateUrl('welcomeIndex'));
        }
        else
        {
    	   $em->remove($event);

    	   $em->flush();

    	   return $this->redirect($this->generateUrl('welcomeIndex'));
        }
    }
}
