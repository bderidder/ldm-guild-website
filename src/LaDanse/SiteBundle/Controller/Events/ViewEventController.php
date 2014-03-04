<?php

namespace LaDanse\SiteBundle\Controller\Events;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use LaDanse\CommonBundle\Helper\LaDanseController;

use LaDanse\SiteBundle\Model\EventModel;

/**
 * @Route("/{id}")
*/
class ViewEventController extends LaDanseController
{
	const EVENT_REPOSITORY = 'LaDanseDomainBundle:Event';

	/**
     * @Route("/view", name="viewEventIndex")
     */
    public function indexAction($id)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

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
            return $this->render('LaDanseSiteBundle::viewEvent.html.twig',
                array('event' => new EventModel($this->getContainerInjector(), $event))
            );
        }
    }

    /**
     * @Route("/delete", name="deleteEventIndex")
     */
    public function deleteAction($id)
    {
    	$em = $this->getDoctrine()->getManager();
    	$repository = $this->getDoctrine()->getRepository(self::EVENT_REPOSITORY);

        /* @var $repository \Doctrine\ORM\EntityRepository */
    	$event = $repository->find($id);

        if (null === $event)
        {
            $this->getLogger()->warn(__CLASS__ . ' the event does not exist in deleteAction', 
                array("event" => $id));

            return $this->redirect($this->generateUrl('welcomeIndex'));
        }
        else
        {
    	   $em->remove($event);

           $this->getLogger()->warn(__CLASS__ . ' removing event in deleteAction', 
                array("event" => $id));

    	   $em->flush();

           $this->addToast('Event removed');

    	   return $this->redirect($this->generateUrl('welcomeIndex'));
        }
    }
}
