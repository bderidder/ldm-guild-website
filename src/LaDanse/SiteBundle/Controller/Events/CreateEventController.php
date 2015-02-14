<?php

namespace LaDanse\SiteBundle\Controller\Events;

use DateTime;
use LaDanse\CommonBundle\Helper\LaDanseController;
use LaDanse\DomainBundle\Entity\Event;
use LaDanse\ServicesBundle\EventListener\Features;
use LaDanse\SiteBundle\Form\Model\EventFormModel;
use LaDanse\SiteBundle\Form\Type\EventFormType;
use LaDanse\SiteBundle\Model\ErrorModel;
use LaDanse\SiteBundle\Security\AuthenticationContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LaDanse\ServicesBundle\EventListener\FeatureUseEvent;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * Class CreateEventController
 *
 * @package LaDanse\SiteBundle\Controller\Events
 */
class CreateEventController extends LaDanseController
{
    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    private $logger;

    /**
     * @var $eventDispatcher EventDispatcherInterface
     * @DI\Inject("event_dispatcher")
     */
    private $eventDispatcher;

    /**
     * @param $request Request
     * @param $onDate string
     *
     * @return Response
     *
     * @Route("/create/{onDate}", name="createEvent")
     */
    public function createAction(Request $request, $onDate = \NULL)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        if (!$authContext->isAuthenticated())
        {
            $this->logger->warning(__CLASS__ . ' the user was not authenticated in indexAction');

            return $this->redirect($this->generateUrl('welcomeIndex'));
        }

        if ($onDate === null)
        {
            $eventDate = new DateTime('tomorrow');
        }
        else
        {
            $eventDate = DateTime::createFromFormat("Ymd", $onDate);

            if ($eventDate === false)
            {
                $eventDate = new DateTime('tomorrow');
            }
        }

        $formModel = new EventFormModel();
        $formModel->setName('');
        $formModel->setDescription('');
        $formModel->setDate($eventDate);
        $formModel->setInviteTime(new DateTime('19:15'));
        $formModel->setStartTime(new DateTime('19:30'));
        $formModel->setEndTime(new DateTime('22:00'));

        $form = $this->createForm(
            new EventFormType(),
            $formModel,
            array('attr' => array('class' => 'form-horizontal', 'novalidate' => ''))
        );

        if ($request->getMethod() == 'POST')
        {
            $form->handleRequest($request);

            $errors = new ErrorModel();

            if ($form->isValid() && $formModel->isValid($errors))
            {
                $this->persistEvent($authContext, $formModel);

                $this->addToast('New event created');

                $this->eventDispatcher->dispatch(
                    FeatureUseEvent::EVENT_NAME,
                    new FeatureUseEvent(
                        Features::EVENT_CREATE,
                        $this->getAuthenticationService()->getCurrentContext()->getAccount()
                    )
                );

                return $this->redirect($this->generateUrl('calendarIndex'));
            }
            else
            {
                return $this->render(
                    'LaDanseSiteBundle:events:createEvent.html.twig',
                    array('form' => $form->createView(), 'errors' => $errors)
                );
            }
        }
        else
        {
            return $this->render(
                'LaDanseSiteBundle:events:createEvent.html.twig',
                array('form' => $form->createView())
            );
        }
    }

    private function persistEvent(AuthenticationContext $authContext, EventFormModel $formModel)
    {
        $commentService = $this->getCommentService();
     
        $commentGroupId = $commentService->createCommentGroup();

        $event = $this->modelToEntity($authContext->getAccount(), $formModel, $commentGroupId);

        $this->logger->info(__CLASS__ . ' persisting event');

        $em = $this->getDoctrine()->getManager();
        $em->persist($event);
        $em->flush();
    }

    private function modelToEntity($organiser, EventFormModel $formModel, $topicId)
    {
        $event = new Event();
        $event->setOrganiser($organiser);
        $event->setName($formModel->getName());
        $event->setDescription($formModel->getDescription());
        $event->setInviteTime($this->createDateTime($formModel->getDate(), $formModel->getInviteTime()));
        $event->setStartTime($this->createDateTime($formModel->getDate(), $formModel->getStartTime()));
        $event->setEndTime($this->createDateTime($formModel->getDate(), $formModel->getEndTime()));
        $event->setTopicId($topicId);

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
