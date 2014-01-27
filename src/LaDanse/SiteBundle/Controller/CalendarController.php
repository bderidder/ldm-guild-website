<?php

namespace LaDanse\SiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use LaDanse\CommonBundle\Helper\LaDanseController;

use LaDanse\SiteBundle\Model\EventModel,
    LaDanse\SiteBundle\Model\CalendarDayModel;

/**
 * @Route("/calendar")
*/
class CalendarController extends LaDanseController
{
    /**
     * @Route("/", name="calendarIndex")
     * @Template("LaDanseSiteBundle::calendar.html.twig")
     */
    public function indexAction(Request $request)
    {
    }

    public function indexPartialAction()
    {
    	$authContext = $this->getAuthenticationService()->getCurrentContext();

        // fetch the Monday we should start with
        $startDate = $this->getStartDate();

        // the algoritm below needs to start on the day before, so we substract a day
        $startDate = $startDate->sub(new \DateInterval("P1D"));

        $calendarDates = array();

        $events = $this->getEvents();
        $eventIndex = 0;

        $currentDate = clone $startDate;

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

                if (($date->format("Y-m-d") < $inviteTime->format("Y-m-d")))
                {
                    break;
                }
                elseif ($date->format("Y-m-d") == $inviteTime->format("Y-m-d"))
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

        if ($authContext->isAuthenticated())
        {
            return $this->render('LaDanseSiteBundle::calendarPartial.html.twig',
                    array('calendarDays' => $calendarDates)
                );
        }
        else
        {
            return $this->render('LaDanseSiteBundle::calendarPartialGuest.html.twig',
                    array('calendarDays' => $calendarDates)
                );
        }
    }

    protected function getEvents()
    {
        $em = $this->getDoctrine()->getManager();

        $startTime = new \DateTime('now');
        $startTime = $startTime->sub(new \DateInterval("P10D"));

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery('SELECT e FROM LaDanse\DomainBundle\Entity\Event e WHERE e.inviteTime > :start ORDER BY e.inviteTime ASC');
        $query->setParameter('start', $startTime);
        
        $events = $query->getResult();

        $eventModels = array();

        foreach($events as $event)
        {
            $eventModels[] = new EventModel($this->getContainerInjector(), $event);
        }

        return $eventModels;
    }

    private function getStartDate()
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

        $calendarStart = date('d/m/Y', strtotime('-' . $day . ' days'));

        return \DateTime::createFromFormat("d/m/Y", $calendarStart);
    }
}
