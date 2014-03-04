<?php

namespace LaDanse\SiteBundle\Controller\Events;

use \DateTime;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;

use LaDanse\CommonBundle\Helper\LaDanseController;

use LaDanse\DomainBundle\Entity\Event;

use LaDanse\SiteBundle\Security\AuthenticationContext;
use LaDanse\SiteBundle\Form\Model\EventFormModel;
use LaDanse\SiteBundle\Form\Type\EventFormType;

use LaDanse\SiteBundle\Model\ErrorModel;

class CreateEventController extends LaDanseController
{
    /**
     * @Route("/create/{onDate}", name="createEvent")
     * @Template("LaDanseSiteBundle::createEvent.html.twig")
     */
    public function createAction(Request $request, $onDate = NULL)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        if (!$authContext->isAuthenticated())
        {
            $this->getLogger()->warn(__CLASS__ . ' the user was not authenticated in indexAction');

            return $this->redirect($this->generateUrl('welcomeIndex'));
        }

        if ($onDate === NULL)
        {
            $eventDate = new DateTime('tomorrow');
        }
        else
        {
            $eventDate = DateTime::createFromFormat("Ymd", $onDate);

            if ($eventDate === FALSE) $eventDate = new DateTime('tomorrow');
        }

    	$formModel = new EventFormModel();
    	$formModel->setName('');
    	$formModel->setDescription('');
    	$formModel->setDate($eventDate);
    	$formModel->setInviteTime(new DateTime('19:15'));
    	$formModel->setStartTime(new DateTime('19:30'));
    	$formModel->setEndTime(new DateTime('22:00'));

    	$form = $this->createForm(new EventFormType(), $formModel, 
            array('attr' => array('class' => 'form-horizontal', 'novalidate' => '')));

        if ($request->getMethod() == 'POST')
        {
    	   $form->handleRequest($request);

            $errors = new ErrorModel();

        	if ($form->isValid() && $formModel->isValid($errors))
        	{
    			$this->persistEvent($authContext, $formModel);

                $this->addToast('New event created');

        		return $this->redirect($this->generateUrl('welcomeIndex'));
    		}
    		else
    		{
    			return $this->render('LaDanseSiteBundle:events:createEvent.html.twig',
    					array('form' => $form->createView(), 'errors' => $errors));
    		}
        }
        else
        {
            return $this->render('LaDanseSiteBundle:events:createEvent.html.twig',
                        array('form' => $form->createView()));
        }	
    }

    private function persistEvent(AuthenticationContext $authContext, EventFormModel $formModel)
    {
    	$event = $this->modelToEntity($authContext->getAccount(), $formModel);

        $this->getLogger()->info(__CLASS__ . ' persisting event');

    	$em = $this->getDoctrine()->getManager();
    	$em->persist($event);
    	$em->flush();
    }

    private function modelToEntity($organiser, EventFormModel $formModel)
    {
        $event = new Event();
        $event->setOrganiser($organiser);
        $event->setName($formModel->getName());
        $event->setDescription($formModel->getDescription());
        $event->setInviteTime($this->createDateTime($formModel->getDate(), $formModel->getInviteTime()));
        $event->setStartTime($this->createDateTime($formModel->getDate(), $formModel->getStartTime()));
        $event->setEndTime($this->createDateTime($formModel->getDate(), $formModel->getEndTime()));

        return $event;
    }

    private function createDateTime(DateTime $datePart, DateTime $timePart)
    {
    	$resultDate = new DateTime();

    	$resultDate->setDate($datePart->format('Y'), $datePart->format('m'), $datePart->format('d'));
    	$resultDate->setTime($timePart->format('H'), $timePart->format('i'), $timePart->format('s'));

    	return $resultDate;
    }
}
