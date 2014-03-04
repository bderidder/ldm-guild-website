<?php

namespace LaDanse\SiteBundle\Controller\Events;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use LaDanse\CommonBundle\Helper\LaDanseController;

use LaDanse\SiteBundle\Model\EventModel;

class RemoveEventController extends LaDanseController
{
	const EVENT_REPOSITORY = 'LaDanseDomainBundle:Event';

    /**
     * @Route("/{id}/remove", name="removeEvent")
     */
    public function removeAction($id)
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
