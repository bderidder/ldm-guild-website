<?php

namespace LaDanse\SiteBundle\Controller\Events;

use LaDanse\CommonBundle\Helper\LaDanseController;
use LaDanse\SiteBundle\Model\EventModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class ViewEventController extends LaDanseController
{
	const EVENT_REPOSITORY = 'LaDanseDomainBundle:Event';

	/**
     * @param string $id
     *
     * @return Response
     *
     * @Route("/{id}", name="viewEvent")
     */
    public function viewAction($id)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        $currentDateTime = new \DateTime();

        if (!$authContext->isAuthenticated())
        {
            $this->getLogger()->warning(__CLASS__ . ' the user was not authenticated in indexAction');

            return $this->redirect($this->generateUrl('welcomeIndex'));
        }

        $em = $this->getDoctrine();
        /* @var $repository \Doctrine\ORM\EntityRepository */
        $repository = $em->getRepository(self::EVENT_REPOSITORY);
        /* @var $event \LaDanse\DomainBundle\Entity\Event */
        $event = $repository->find($id);

        if (null === $event)
        {
            $this->getLogger()->warning(
                __CLASS__ . ' the event does not exist in indexAction',
                array("event" => $id)
            );

            return $this->redirect($this->generateUrl('calendarIndex'));
        }
        else
        {
            return $this->render(
                'LaDanseSiteBundle:events:viewEvent.html.twig',
                array(
                    'isFuture' => ($event->getInviteTime() > $currentDateTime),
                    'event' => new EventModel($this->getContainerInjector(), $event))
            );
        }
    }
}
