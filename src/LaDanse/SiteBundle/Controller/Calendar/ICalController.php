<?php

namespace LaDanse\SiteBundle\Controller\Calendar;

use LaDanse\CommonBundle\Helper\LaDanseController;
use LaDanse\SiteBundle\Model\CalendarDayModel;
use LaDanse\SiteBundle\Model\EventModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Eluceo\iCal\Component as iCal;

use JMS\DiExtraBundle\Annotation as DI;

class ICalController extends LaDanseController
{
    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    private $logger;

    /**
     * @param $request Request
     *
     * @return Response
     *
     * @Route("/ical", name="icalIndex")
     */
    public function indexAction(Request $request)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        /*
        if (!$authContext->isAuthenticated())
        {
            $this->logger->warning(__CLASS__ . ' the user was not authenticated in calendarIndex');

            return $this->redirect($this->generateUrl('icalIndex'));
        }
        */

        $vCalendar = new iCal\Calendar('www.ladanse.org');

        // we suggest a refresh every 30 minutes
        $vCalendar->setPublishedTTL('P30M');

        $allEvents = $this->getAllEvents();

        /** @var \LaDanse\SiteBundle\Model\EventModel $event */
        foreach($allEvents as $event)
        {
            $vEvent = new iCal\Event();
            $vEvent->setDtStart($event->getInviteTime());
            $vEvent->setDtEnd($event->getEndTime());
            $vEvent->setSummary($event->getName());

            $vEvent->setUseTimezone(true);

            $vCalendar->addComponent($vEvent);
        }

        return new Response(
            $vCalendar->render(),
            Response::HTTP_OK,
            array(
                'content-type' => 'text/calendar; charset=utf-8',
                'content-disposition' => 'attachment; filename="cal.ics"'
                )
        );
    }

    protected function getAllEvents()
    {
        $em = $this->getDoctrine()->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery('SELECT e FROM LaDanse\DomainBundle\Entity\Event e ORDER BY e.inviteTime DESC');

        $events = $query->getResult();

        $eventModels = array();

        foreach($events as $event)
        {
            $eventModels[] = new EventModel($this->getContainerInjector(), $event);
        }

        return $eventModels;
    }
}
