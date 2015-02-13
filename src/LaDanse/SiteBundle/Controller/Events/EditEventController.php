<?php

namespace LaDanse\SiteBundle\Controller\Events;

use DateTime;
use LaDanse\CommonBundle\Helper\LaDanseController;
use LaDanse\DomainBundle\Entity\Event;
use LaDanse\SiteBundle\Form\Model\EventFormModel;
use LaDanse\SiteBundle\Form\Type\EventFormType;
use LaDanse\SiteBundle\Model\ErrorModel;
use LaDanse\SiteBundle\Model\EventModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use JMS\DiExtraBundle\Annotation as DI;

class EditEventController extends LaDanseController
{
	const EVENT_REPOSITORY = 'LaDanseDomainBundle:Event';

    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    private $logger;

	/**
     * @param $request Request
     * @param $id string
     *
     * @return Response
     *
     * @Route("/{id}/edit", name="editEvent")
     */
    public function editAction(Request $request, $id)
    {
    	$authContext = $this->getAuthenticationService()->getCurrentContext();

        if (!$authContext->isAuthenticated())
        {
            $this->logger->warning(__CLASS__ . ' the user was not authenticated in indexAction');

            return $this->redirect($this->generateUrl('welcomeIndex'));
        }

    	$em = $this->getDoctrine()->getManager();
        /* @var $repository \Doctrine\ORM\EntityRepository */
    	$repository = $this->getDoctrine()->getRepository(self::EVENT_REPOSITORY);
        /* @var $event \LaDanse\DomainBundle\Entity\Event */
    	$event = $repository->find($id);

        if (null === $event)
        {
            $this->logger->warning(__CLASS__ . ' the event does not exist in indexAction',
                array("event" => $id));

            return $this->redirect($this->generateUrl('calendarIndex'));
        }

        $currentDateTime = new \DateTime();
        if ($event->getInviteTime() <= $currentDateTime)
        {
            return $this->redirect($this->generateUrl('viewEvent', array('id' => $id)));
        }

        if (!($event->getOrganiser()->getId() === $authContext->getAccount()->getId()))
        {
            $this->logger->warning(__CLASS__ . ' the user is not the organiser of the event in indexAction');

        	return $this->redirect($this->generateUrl('calendarIndex'));
        }
        
        $formModel = $this->entityToModel($event);

        $form = $this->createForm(new EventFormType(), $formModel, 
            array('attr' => array('class' => 'form-horizontal', 'novalidate' => '')));

        if ($request->getMethod() == 'POST')
        {
        	$form->handleRequest($request);

            $errors = new ErrorModel();

        	if ($form->isValid() && $formModel->isValid($errors))
        	{
        		$this->modelToEntity($formModel, $event);

                $this->logger->info(__CLASS__ . ' persisting changes to event indexAction');

        		$em->flush();

                $this->addToast('Event updated');

        		return $this->redirect($this->generateUrl('viewEvent', array('id' => $id)));
        	}
            else
            {
                return $this->render('LaDanseSiteBundle:events:editEvent.html.twig',
                        array('event' => new EventModel($this->getContainerInjector(), $event, $authContext->getAccount()),
                              'form' => $form->createView(),
                              'errors' => $errors));    
            }
    	}
    	else
    	{
        	return $this->render('LaDanseSiteBundle:events:editEvent.html.twig',
						array('event' => new EventModel($this->getContainerInjector(), $event, $authContext->getAccount()), 'form' => $form->createView()));
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
