<?php

namespace LaDanse\SiteBundle\Controller\Events;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use LaDanse\CommonBundle\Helper\LaDanseController;

use LaDanse\SiteBundle\Model\EventModel;

class ViewEventController extends LaDanseController
{
	const EVENT_REPOSITORY = 'LaDanseDomainBundle:Event';

	/**
     * @Route("/{id}", name="viewEvent")
     */
    public function viewAction($id)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        $currentDateTime = new \DateTime();

        if (!$authContext->isAuthenticated())
        {
            $this->getLogger()->warn(__CLASS__ . ' the user was not authenticated in indexAction');

            return $this->redirect($this->generateUrl('welcomeIndex'));
        }

        $em = $this->getDoctrine();
        /* @var $repository \Doctrine\ORM\EntityRepository */
        $repository = $em->getRepository(self::EVENT_REPOSITORY);
        /* @var $event \LaDanse\DomainBundle\Entity\Event */
    	$event = $repository->find($id);

        if (null === $event)
        {
            $this->getLogger()->warn(__CLASS__ . ' the event does not exist in indexAction', 
                array("event" => $id));

            return $this->redirect($this->generateUrl('welcomeIndex'));
        }
        else
        {
            if ($event->getInviteTime() > $currentDateTime)
            {
                return $this->render('LaDanseSiteBundle:events:viewFutureEvent.html.twig',
                    array('event' => new EventModel($this->getContainerInjector(), $event))
                );
            }
            else
            {
                return $this->render('LaDanseSiteBundle:events:viewPastEvent.html.twig',
                    array('event' => new EventModel($this->getContainerInjector(), $event))
                );
            }
        }
    }
}
