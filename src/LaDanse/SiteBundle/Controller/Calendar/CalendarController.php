<?php

namespace LaDanse\SiteBundle\Controller\Calendar;

use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use LaDanse\CommonBundle\Helper\LaDanseController;

use LaDanse\SiteBundle\Model\EventModel,
    LaDanse\SiteBundle\Model\CalendarDayModel;

class CalendarController extends LaDanseController
{
    const COMPARE_DATE_FORMAT = "Y-m-d";

    /**
     * @Route("/", name="calendarIndex")
     * @Template("LaDanseSiteBundle:calendar:calendar.html.twig")
     */
    public function indexAction(Request $request)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        if (!$authContext->isAuthenticated())
        {
            $this->getLogger()->warn(__CLASS__ . ' the user was not authenticated in calendarIndex');

            return $this->redirect($this->generateUrl('welcomeIndex'));
        }

        $page = $this->sanitizePage($request->query->get('page'));

        return $this->render('LaDanseSiteBundle:calendar:calendar.html.twig',
            array('page' => $page)
        );
    }

    public function indexPartialAction(Request $request, $page)
    {
        // fetch the Monday we should start with
        $startDate = $this->getStartDate($page);

        // the algoritm below needs to start on the day before, so we substract a day
        $startDate = $startDate->sub(new \DateInterval("P1D"));

        $calendarDates = array();

        //$startDate = new \DateTime('now');
        //$startDate = $startDate->sub(new \DateInterval("P10D"));
        $events = $this->getEvents($startDate);

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
        $events = $this->getEvents($startDate);


        return $this->render('LaDanseSiteBundle:calendar:calendarTilePartial.html.twig',
                    array('events' => $events)
                );
    }

    protected function getEvents($startDate)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery('SELECT e FROM LaDanse\DomainBundle\Entity\Event e WHERE e.inviteTime > :start ORDER BY e.inviteTime ASC');
        $query->setParameter('start', $startDate);
        
        $events = $query->getResult();

        $eventModels = array();

        foreach($events as $event)
        {
            $eventModels[] = new EventModel($this->getContainerInjector(), $event);
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
