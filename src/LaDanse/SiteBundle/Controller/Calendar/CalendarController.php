<?php

namespace LaDanse\SiteBundle\Controller\Calendar;

use LaDanse\CommonBundle\Helper\LaDanseController;
use LaDanse\SiteBundle\Model\CalendarDayModel;
use LaDanse\SiteBundle\Model\EventModel;
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

        $page = $this->sanitizePage($request->query->get('page'));

        return $this->render('LaDanseSiteBundle:calendar:calendar.html.twig',
            array('page' => $page)
        );
    }

    public function indexPartialAction($page)
    {
        /* @var $startDate \DateTime */
        // fetch the Monday we should start with
        $startDate = $this->getStartDate($page);

        // the algoritm below needs to start on the day before, so we substract a day
        $startDate = $startDate->sub(new \DateInterval("P1D"));

        $calendarDates = array();

        $events = $this->getEvents($startDate, $this->getAuthenticationService()->getCurrentContext()->getAccount());

        $eventIndex = 0;

        $currentDate = clone $startDate;

        // we show 28 days, that is 4 weeks
        for($i = 0; $i < 28; $i++)
        {
            $date = clone $currentDate;

            $date->add(new \DateInterval("P1D"));

            $calendarDateModel = new CalendarDayModel($this->getContainerInjector(), $date);

            if ($i === 0) $calendarDateModel->setShowMonth(true);

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

        return $this->render('LaDanseSiteBundle:calendar:calendarPartial.html.twig',
                array('calendarDays' => $calendarDates, 'pager' => $this->createPager($page))
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

    private function getStartDate($page)
    {
        $day = date('w');

        if ($day === 0)
        {
            // it's Sunday but in Europe we start our weeks on Monday
            $day = 7;
        }
        
        // days are not from Monday (1) to Sunday (7)

        if ($day < 3)
        {
            // it's monday or tuesday, we need to include the previous week in our
            // calendar to show the current raid week completely

            $day = $day + 6;
        }
        else
        {
            $day = $day - 1;
        }

        $currentMonthStart = strtotime('-' . $day . ' days');

        if ($page != 0)
        {
            $calendarStart = date('d/m/Y', strtotime(($page * 28) .' days', $currentMonthStart));

            return \DateTime::createFromFormat("d/m/Y", $calendarStart);
        }
        else
        {
            return \DateTime::createFromFormat("d/m/Y", date('d/m/Y', $currentMonthStart));
        }
    }

    private function sanitizePage($strPage)
    {
        $intPage = intval($strPage);

        if ($intPage < -10)
        {
            $intPage = -10;
        }
        elseif ($intPage > 10)
        {
            $intPage = 10;
        }

        return $intPage;
    }

    private function createPager($page)
    {
        if ($page <= -10)
        {
            return (object)array(
                'nextPage' => -9
            );
        }
        elseif ($page >= 10)
        {
            return (object)array(
                'previousPage' => 9
            );
        }
        else
        {
            return (object)array(
                'previousPage' => $page - 1,
                'nextPage' => $page + 1
            );
        }
    }
}
