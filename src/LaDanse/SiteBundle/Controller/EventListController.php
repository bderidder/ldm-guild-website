<?php

namespace LaDanse\SiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use LaDanse\CommonBundle\Helper\LaDanseController;

use LaDanse\SiteBundle\Model\EventModel;

/**
 * @Route("/events")
*/
class EventListController extends LaDanseController
{
    /**
     * @Route("/", name="eventListIndex")
     * @Template("LaDanseSiteBundle::eventList.html.twig")
     */
    public function indexAction(Request $request)
    {
    }

    public function indexPartialAction()
    {
    	$authContext = $this->getAuthenticationService()->getCurrentContext();

        $em = $this->getDoctrine()->getManager();

        /* @var $query \Doctrine\ORM\Query */
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
            return $this->render('LaDanseSiteBundle::listEventsPartial.html.twig',
                    array('events' => $eventModels)
                );
        }
        else
        {
            return $this->render('LaDanseSiteBundle::listEventsPartialGuest.html.twig',
                    array('events' => $eventModels)
                );
        }
    }
}
