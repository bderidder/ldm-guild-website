<?php

namespace LaDanse\SiteBundle\Controller;

use \DateTime;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use LaDanse\DomainBundle\Entity\Account;
use LaDanse\DomainBundle\Entity\Event;

use LaDanse\CommonBundle\Helper\LaDanseController;

use LaDanse\SiteBundle\Security\AuthenticationContext;

use LaDanse\SiteBundle\Form\Model\EventFormModel;
use LaDanse\SiteBundle\Form\Type\EventFormType;

/**
 * @Route("/event/{id}/edit")
*/
class EditEventController extends LaDanseController
{
	const EVENT_REPOSITORY = 'LaDanseDomainBundle:Event';

	/**
     * @Route("/", name="editEventIndex")
     * @Template("LaDanseSiteBundle::editEvent.html.twig")
     */
    public function indexAction(Request $request, $id)
    {
    	$authContext = $this->getAuthenticationService()->getCurrentContext();

        if (!$authContext->isAuthenticated())
        {
            $this->getLogger()->warn(__CLASS__ . ' the user was not authenticated in indexAction');

            return $this->redirect($this->generateUrl('welcomeIndex'));
        }

    	$em = $this->getDoctrine()->getManager();
    	$repository = $this->getDoctrine()->getRepository(self::EVENT_REPOSITORY);
    	$event = $repository->find($id);

        if (null === $event)
        {
            $this->getLogger()->warn(__CLASS__ . ' the event does not exist in indexAction', 
                array("event" => $id));

            return $this->redirect($this->generateUrl('welcomeIndex'));
        }

        if (!($event->getOrganiser()->getId() === $authContext->getAccount()->getId()))
        {
            $this->getLogger()->warn(__CLASS__ . ' the user is not the organiser of the event in indexAction', 
                array('event' => $id, 'user' => $authContext->getAccount()->getId()));

        	return $this->redirect($this->generateUrl('welcomeIndex'));
        }
        
        $formModel = $this->entityToModel($event);

        $form = $this->createForm(new EventFormType(), $formModel, array('attr' => array('class' => 'form-horizontal')));

        if ($request->getMethod() == 'POST')
        {
        	$form->handleRequest($request);

        	if ($form->isValid())
        	{
        		$this->modelToEntity($formModel, $event);

                $this->getLogger()->info(__CLASS__ . ' persisting changes to event indexAction');

        		$em->flush();

        		return $this->redirect($this->generateUrl('welcomeIndex'));
        	}
    	}
    	else
    	{
        	return $this->render('LaDanseSiteBundle::editEvent.html.twig',
						array('form' => $form->createView()));	
    	}
    }

    private function entityToModel(Event $event)
    {
    	$formModel = new EventFormModel();
    	$formModel->setName($event->getName());
    	$formModel->setDescription($event->getDescription());
    	$formModel->setDate($event->getStartTime());
    	$formModel->setInviteTime($event->getInviteTime());
    	$formModel->setStartTime($event->getStartTime());
    	$formModel->setEndTime($event->getEndTime());

    	return $formModel;
    }

    private function modelToEntity(EventFormModel $formModel, Event $event)
    {
        $event->setName($formModel->getName());
        $event->setDescription($formModel->getDescription());
        $event->setInviteTime($this->createDateTime($formModel->getDate(), $formModel->getInviteTime()));
        $event->setStartTime($this->createDateTime($formModel->getDate(), $formModel->getStartTime()));
        $event->setEndTime($this->createDateTime($formModel->getDate(), $formModel->getEndTime()));
    }

    private function createDateTime(DateTime $datePart, DateTime $timePart)
    {
    	$resultDate = new DateTime();

    	$resultDate->setDate($datePart->format('Y'), $datePart->format('m'), $datePart->format('d'));
    	$resultDate->setTime($timePart->format('H'), $timePart->format('i'), $timePart->format('s'));

    	return $resultDate;
    }
}
