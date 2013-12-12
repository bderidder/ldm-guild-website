<?php

namespace LaDanse\SiteBundle\Controller;

use \DateTime;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use LaDanse\CommonBundle\Helper\LaDanseController;

use LaDanse\DomainBundle\Entity\Event;

use LaDanse\SiteBundle\Security\AuthenticationContext;
use LaDanse\SiteBundle\Form\Model\NewEventFormModel;
use LaDanse\SiteBundle\Form\Type\NewEventFormType;

/**
 * @Route("/Events/Create")
*/
class CreateEventController extends LaDanseController
{
	/**
     * @Route("/", name="createEventIndex")
     * @Template("LaDanseSiteBundle::createEvent.html.twig")
     */
    public function indexAction(Request $request)
    {
		$authContext = new AuthenticationContext($this->get('LaDanse.ContainerInjector'), $request);

    	$formModel = new NewEventFormModel();
    	$formModel->setName('A name');
    	$formModel->setDescription('A description');
    	$formModel->setDate(new DateTime('tomorrow'));
    	$formModel->setInviteTime(new DateTime('19:15'));
    	$formModel->setStartTime(new DateTime('19:30'));
    	$formModel->setEndTime(new DateTime('22:00'));

    	$form = $this->createForm(new NewEventFormType(), $formModel);

    	$form->handleRequest($request);

    	if ($form->isValid())
    	{
			$this->persistEvent($authContext, $formModel);

    		return $this->redirect($this->generateUrl('viewEventsIndex'));
		}
		else
		{
			return $this->render('LaDanseSiteBundle::createEvent.html.twig',
					array('form' => $form->createView()));
		}	
    }

    private function persistEvent(AuthenticationContext $authContext, NewEventFormModel $formModel)
    {
    	$event = new Event();
    	$event->setOrganiser($authContext->getAccount());
    	$event->setName($formModel->getName());
    	$event->setDescription($formModel->getDescription());
    	$event->setInviteTime($this->createDateTime($formModel->getDate(), $formModel->getInviteTime()));
    	$event->setStartTime($this->createDateTime($formModel->getDate(), $formModel->getStartTime()));
    	$event->setEndTime($this->createDateTime($formModel->getDate(), $formModel->getEndTime()));

    	$em = $this->getDoctrine()->getManager();
    	$em->persist($event);
    	$em->flush();
    }

    private function createDateTime(DateTime $datePart, DateTime $timePart)
    {
    	$resultDate = new DateTime();

    	$resultDate->setDate($datePart->format('Y'), $datePart->format('m'), $datePart->format('d'));
    	$resultDate->setTime($timePart->format('H'), $timePart->format('i'), $timePart->format('s'));

    	return $resultDate;
    }
}
