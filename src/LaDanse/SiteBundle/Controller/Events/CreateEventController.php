<?php

namespace LaDanse\SiteBundle\Controller\Events;

use DateTime;
use LaDanse\CommonBundle\Helper\LaDanseController;
use LaDanse\DomainBundle\Entity\Account;
use LaDanse\ServicesBundle\Service\Event\EventService;
use LaDanse\SiteBundle\Form\Model\EventFormModel;
use LaDanse\SiteBundle\Form\Type\EventFormType;
use LaDanse\SiteBundle\Model\ErrorModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * @param $request Request
     * @param $onDate string
     *
     * @return Response
     *
     * @Route("/create/{onDate}", name="createEvent")
     */
    public function createAction(Request $request, $onDate = \NULL)
    {
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
                $this->persistEvent($this->getAccount(), $formModel);

                $this->addToast('New event created');

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

    private function persistEvent(Account $account, EventFormModel $formModel)
    {
        /** @var $eventService EventService */
        $eventService = $this->get(EventService::SERVICE_NAME);

        $eventService->createEvent(
            $account,
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
