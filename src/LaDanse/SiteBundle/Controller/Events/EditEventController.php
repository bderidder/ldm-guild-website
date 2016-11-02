<?php

namespace LaDanse\SiteBundle\Controller\Events;

use DateTime;
use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\Event;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Service\Authorization\ResourceByValue;
use LaDanse\ServicesBundle\Service\Authorization\SubjectReference;
use LaDanse\ServicesBundle\Service\Event\EventDoesNotExistException;
use LaDanse\ServicesBundle\Service\Event\EventService;
use LaDanse\SiteBundle\Common\LaDanseController;
use LaDanse\SiteBundle\Form\Model\EventFormModel;
use LaDanse\SiteBundle\Form\Type\EventFormType;
use LaDanse\SiteBundle\Model\ErrorModel;
use LaDanse\SiteBundle\Model\EventModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

class EditEventController extends LaDanseController
{
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
        $account = $authContext->getAccount();

        /** @var EventService $eventService */
        $eventService = $this->get(EventService::SERVICE_NAME);

        /** @var Event $event */
        $event = null;

        try
        {
            $event = $eventService->getEventById($id);
        }
        catch(EventDoesNotExistException $e)
        {
            $this->logger->warning(
                __CLASS__ . ' the event does not exist in indexAction',
                ["event" => $id]
            );

            return $this->redirect($this->generateUrl('calendarIndex'));
        }
        catch(\Exception $e)
        {
            $this->logger->warning(
                __CLASS__ . ' unexpected error',
                [
                    "throwable" => $e,
                    "event"     => $id,
                    "account"   => $account->getId()
                ]
            );

            return $this->redirect($this->generateUrl('viewEvent', ['id' => $id]));
        }

        /* verify that the event is not in the past */
        $currentDateTime = new \DateTime();
        if ($event->getInviteTime() <= $currentDateTime)
        {
            return $this->redirect($this->generateUrl('viewEvent', ['id' => $id]));
        }

        /* verify that the user can edit this particular event */
        if (!$this->isAuthorized(
            new SubjectReference($this->getAccount()),
            ActivityType::EVENT_EDIT,
            new ResourceByValue(Event::class, $event->getId(), $event)))
        {
            $this->logger->warning(__CLASS__ . ' the user is not authorized to edit event in indexAction');

        	return $this->redirect($this->generateUrl('calendarIndex'));
        }
        
        $formModel = $this->entityToModel($event);

        $form = $this->createForm(EventFormType::class, $formModel,
            ['attr' => ['class' => 'form-horizontal', 'novalidate' => '']]);

        if ($request->getMethod() == 'POST')
        {
        	$form->handleRequest($request);

            $errors = new ErrorModel();

        	if ($form->isValid() && $formModel->isValid($errors))
        	{
                $this->logger->info(__CLASS__ . ' persisting changes to event indexAction');

        		$this->updateEvent($formModel, $id);

                $this->addToast('Event updated');

        		return $this->redirect($this->generateUrl('viewEvent', ['id' => $id]));
        	}
            else
            {
                return $this->render(
                    'LaDanseSiteBundle:events:editEvent.html.twig',
                    [
                        'event' => new EventModel($event, $this->getAccount()),
                        'form' => $form->createView(),
                        'errors' => $errors
                    ]
                );
            }
    	}
    	else
    	{
        	return $this->render(
                'LaDanseSiteBundle:events:editEvent.html.twig',
                [
                    'event' => new EventModel($event, $this->getAccount()),
                    'form' => $form->createView()
                ]
            );
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

    private function updateEvent(EventFormModel $formModel, $eventId)
    {
        /** @var EventService $eventService */
        $eventService = $this->get(EventService::SERVICE_NAME);

        $eventService->updateEvent(
            $eventId,
            $formModel->getName(),
            $formModel->getDescription(),
            $this->createDateTime($formModel->getDate(), $formModel->getInviteTime()),
            $this->createDateTime($formModel->getDate(), $formModel->getStartTime()),
            $this->createDateTime($formModel->getDate(), $formModel->getEndTime())
        );

        $this->logger->info(__CLASS__ . ' persisting event');
    }

    private function createDateTime(DateTime $datePart, DateTime $timePart)
    {
    	$resultDate = new DateTime();

    	$resultDate->setDate($datePart->format('Y'), $datePart->format('m'), $datePart->format('d'));
    	$resultDate->setTime($timePart->format('H'), $timePart->format('i'), $timePart->format('s'));

    	return $resultDate;
    }
}
