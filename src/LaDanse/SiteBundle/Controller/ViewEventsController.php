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

        $em = $this->getDoctrine()->getManager();
        
        $query = $em->createQuery('SELECT e FROM LaDanse\DomainBundle\Entity\Event e WHERE e.inviteTime > :now ORDER BY e.inviteTime ASC');
        $query->setParameter('now', new \DateTime('now'));
        
        $events = $query->getResult();

        $eventModels = array();

        foreach($events as $event)
        {
            $eventModels[] = new EventModel($this->getContainerInjector(), $event);
        }

        if ($authContext->isAuthenticated())
        {
            return $this->render('LaDanseSiteBundle::viewEvents.html.twig',
                    array('events' => $eventModels)
                );
        }
        else
        {
            return $this->render('LaDanseSiteBundle::viewEventsGuest.html.twig',
                    array('events' => $eventModels)
                );
        }
    }
}
