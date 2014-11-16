<?php

namespace LaDanse\SiteBundle\Controller\Events;

use LaDanse\CommonBundle\Helper\LaDanseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class RemoveEventController extends LaDanseController
{
	const EVENT_REPOSITORY = 'LaDanseDomainBundle:Event';

    /**
     * @param string $id
     *
     * @return Response
     *
     * @Route("/{id}/remove", name="removeEvent")
     */
    public function removeAction($id)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        if (!$authContext->isAuthenticated())
        {
            $this->getLogger()->warning(__CLASS__ . ' the user was not authenticated in calendarIndex');

            return $this->redirect($this->generateUrl('welcomeIndex'));
        }

    	$em = $this->getDoctrine()->getManager();
    	$repository = $this->getDoctrine()->getRepository(self::EVENT_REPOSITORY);

        /* @var $repository \Doctrine\ORM\EntityRepository */
    	$event = $repository->find($id);

        if (null === $event)
        {
            $this->getLogger()->warning(__CLASS__ . ' the event does not exist in deleteAction',
                array("event" => $id));

            return $this->redirect($this->generateUrl('calendarIndex'));
        }
        else
        {
            $currentDateTime = new \DateTime();
            if ($event->getInviteTime() <= $currentDateTime)
            {
                return $this->redirect($this->generateUrl('viewEvent', array('id' => $id)));
            }

            $this->getForumService()->removeTopic($event->getTopicId());

            $em->remove($event);

            $this->getLogger()->warning(__CLASS__ . ' removing event in deleteAction',
                array("event" => $id));

            $em->flush();

            $this->addToast('Event removed');

            return $this->redirect($this->generateUrl('menuIndex'));
        }
    }
}
