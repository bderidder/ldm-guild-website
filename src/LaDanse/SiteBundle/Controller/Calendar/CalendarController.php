<?php

namespace LaDanse\SiteBundle\Controller\Calendar;

use LaDanse\CommonBundle\Helper\LaDanseController;
use LaDanse\SiteBundle\Model\Calendar\CalendarDayModel;
use LaDanse\SiteBundle\Model\Calendar\CalendarMonthModel;
use LaDanse\SiteBundle\Model\EventModel;
use LaDanse\SiteBundle\Model\Calendar\RaidWeekModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use LaDanse\DomainBundle\Entity\Account;

use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;

use JMS\DiExtraBundle\Annotation as DI;

class CalendarController extends LaDanseController
{
    const COMPARE_DATE_FORMAT = "Y-m-d";

    const QUERY_DATE_FORMAT = "Ymd";

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
     *
     * @return Response
     *
     * @Route("/", name="calendarIndex")
     */
    public function indexAction(Request $request)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        if (!$authContext->isAuthenticated())
        {
            $this->logger->warning(__CLASS__ . ' the user was not authenticated in calendarIndex');

            return $this->redirect($this->generateUrl('welcomeIndex'));
        }

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::CALENDAR_VIEW,
                $this->getAuthenticationService()->getCurrentContext()->getAccount())
        );

        $showDateStr = $request->query->get('showDate');

        if ($showDateStr === null)
        {
            $showDate = new \DateTime('today');
        }
        else
        {
            $showDate = \DateTime::createFromFormat(CalendarController::QUERY_DATE_FORMAT, $showDateStr);

            if ($showDate === false)
            {
                $showDate = new \DateTime('today');
            }
        }

        return $this->render('LaDanseSiteBundle:calendar:calendar.html.twig',
            array('showDate' => $showDate)
        );
    }

    public function indexPartialAction(\DateTime $showDate)
    {
        /** @var CalendarMonthModel $calendarMonthModel */
        $calendarMonthModel = new CalendarMonthModel($showDate);

        // create a RaidWeekModel representing the current raid week
        /** @var RaidWeekModel $raidWeek */
        $raidWeek = new RaidWeekModel(new \DateTime());

        if (!$calendarMonthModel->containsDate($raidWeek->getFirstDate())
            &&
            $calendarMonthModel->containsDate($raidWeek->getLastDate()))
        {
            $calendarMonthModel->shiftOneWeekBack();
        }

        /* @var \DateTime $startDate */
        $startDate = $calendarMonthModel->getStartDate();

        // the algoritm below needs to start on the day before, so we substract a day
        $startDate = $startDate->modify('-1days');

        $calendarDates = array();

        $events = $this->getEvents($startDate, $this->getAuthenticationService()->getCurrentContext()->getAccount());

        $eventIndex = 0;

        $currentDate = clone $startDate;

        // we show 28 days, that is 4 weeks
        for($i = 0; $i < 28; $i++)
        {
            $date = clone $currentDate;

            $date->modify('+1days');

            $calendarDateModel = new CalendarDayModel($this->getContainerInjector(), $date);

            $calendarDateModel->setIsInCurrentRaidWeek($raidWeek->inRaidWeek($date));

            // show the month in the first cell (first row, first day)
            if ($i === 0) $calendarDateModel->setShowMonth(true);

            // show the month if we change month
            if ($date->format("d") === "01") $calendarDateModel->setShowMonth(true);

            $calendarDates[] = $calendarDateModel;

            while($eventIndex < count($events))
            {
                /* @var $currentEvent EventModel */
                $currentEvent = $events[$eventIndex];
                $inviteTime = $currentEvent->getInviteTime();

                if (($date->format(CalendarController::COMPARE_DATE_FORMAT) 
                    < $inviteTime->format(CalendarController::COMPARE_DATE_FORMAT)))
                {
                    break;
                }
                elseif ($date->format(CalendarController::COMPARE_DATE_FORMAT) 
                    == $inviteTime->format(CalendarController::COMPARE_DATE_FORMAT))
                {
                    $calendarDateModel->addEvent($currentEvent);

                    $eventIndex++;
                }
                else
                {
                    $eventIndex++;
                }
            }

            $currentDate = $date;
        }

        $previousPageDate = clone $startDate;
        $previousPageDate->sub(new \DateInterval('P21D'));

        $nextPageDate = clone $startDate;
        $nextPageDate->add(new \DateInterval('P35D'));

        $todayDate = new \DateTime('today');

        return $this->render('LaDanseSiteBundle:calendar:calendarPartial.html.twig',
                array(
                    'calendarDays'     => $calendarDates,
                    'previousPageDate' => $previousPageDate->format(CalendarController::QUERY_DATE_FORMAT),
                    'todayPageDate'    => $todayDate->format(CalendarController::QUERY_DATE_FORMAT),
                    'nextPageDate'     => $nextPageDate->format(CalendarController::QUERY_DATE_FORMAT))
        );
    }

    public function tilePartialAction()
    {
        $startDate = new \DateTime('now');
        $events = $this->getEvents($startDate, $this->getAuthenticationService()->getCurrentContext()->getAccount());

        return $this->render('LaDanseSiteBundle:calendar:calendarTilePartial.html.twig',
                    array('events' => $events)
                );
    }

    protected function getEvents($startDate, Account $currentUser)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery('SELECT e FROM LaDanse\DomainBundle\Entity\Event e WHERE e.inviteTime > :start ORDER BY e.inviteTime ASC');
        $query->setParameter('start', $startDate);
        
        $events = $query->getResult();

        $eventModels = array();

        foreach($events as $event)
        {
            $eventModels[] = new EventModel($this->getContainerInjector(), $event, $currentUser);
        }

        return $eventModels;
    }
}
