<?php

namespace LaDanse\SiteBundle\Controller\Events;

use LaDanse\CommonBundle\Helper\LaDanseController;
use LaDanse\DomainBundle\Entity\SignUpType;
use LaDanse\ServicesBundle\Service\Event\Command\EventDoesNotExistException;
use LaDanse\ServicesBundle\Service\Event\EventService;
use LaDanse\SiteBundle\Form\Model\SignUpFormModel;
use LaDanse\SiteBundle\Form\Type\SignUpFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * @Route("/{eventId}/signup")
*/
class CreateSignUpController extends LaDanseController
{
    const EVENT_REPOSITORY = 'LaDanseDomainBundle:Event';

    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    private $logger;

	/**
     * @param $request Request
     * @param $eventId string
     *
     * @return Response
     *
     * @Route("/create", name="createSignUp")
     */
    public function createAction(Request $request, $eventId)
    {
        /** @var $eventService EventService */
        $eventService = $this->get(EventService::SERVICE_NAME);

        $event = null;

        try
        {
            $event = $eventService->getEventById($eventId);
        }
        catch (EventDoesNotExistException $e)
        {
            $this->logger->warning(
                __CLASS__ . ' the event does not exist in createAction',
                array("event id" => $eventId)
            );

            return $this->redirect($this->generateUrl('calendarIndex'));
        }

        $formModel = new SignUpFormModel();

        $form = $this->createForm(
            new SignUpFormType(),
            $formModel,
            array('attr' => array('class' => 'form-horizontal', 'novalidate' => ''))
        );

        $form->handleRequest($request);

        if ($form->isValid() && $formModel->isValid($form))
        {
            try
            {
                $eventService->createSignUp(
                    $eventId,
                    $this->getAccount(),
                    $formModel->getType(),
                    $formModel->getRoles()
                );

                $this->addToast('Signed up');
            }
            catch (\Exception $e)
            {
                $this->logger->error(
                    __CLASS__ . ' could not create sign up',
                    array("exception" => $e)
                );

                $this->addToast('Error saving sign up');
            }

            return $this->redirect($this->generateUrl('viewEvent', array('id' => $eventId)));
        }
        else
        {
            return $this->render(
                "LaDanseSiteBundle:events:createSignUp.html.twig",
                array('event' => $event, 'form' => $form->createView())
            );
        }
    }

    /**
     * @param $eventId string
     *
     * @return Response
     *
     * @Route("/createabsence", name="createAbsence")
     */
    public function createAbsenceAction($eventId)
    {
        /** @var $eventService EventService */
        $eventService = $this->get(EventService::SERVICE_NAME);

        try
        {
            $eventService->createSignUp(
                $eventId,
                $this->getAccount(),
                SignUpType::ABSENCE
            );

            $this->addToast('Absence saved');
        }
        catch (\Exception $e)
        {
            $this->logger->error(
                __CLASS__ . ' could not create sign up',
                array("exception" => $e)
            );

            $this->addToast('Error saving absence');
        }

        return $this->redirect($this->generateUrl('viewEvent', array('id' => $eventId)));
    }
}
