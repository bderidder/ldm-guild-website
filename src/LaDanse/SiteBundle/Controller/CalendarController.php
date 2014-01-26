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

        $startDate = \DateTime::createFromFormat("d/m/Y", "19/01/2014");

        $calendarDates = array();

        $events = $this->getEvents();
        $eventIndex = 0;

        $currentDate = clone $startDate;

        for($i = 0; $i < 28; $i++)
        {
            $date = clone $currentDate;

            $date->add(new \DateInterval("P1D"));

            $calendarDateModel = new CalendarDayModel($this->getContainerInjector(), $date);

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
            }

            $currentDate = $date;
        }

        return $this->render('LaDanseSiteBundle::calendarPartial.html.twig',
                    array('calendarDays' => $calendarDates)
                );
    }

    protected function getEvents()
    {
        $em = $this->getDoctrine()->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery('SELECT e FROM LaDanse\DomainBundle\Entity\Event e WHERE e.inviteTime > :now ORDER BY e.inviteTime ASC');
        $query->setParameter('now', new \DateTime('now'));
        
        $events = $query->getResult();

        $eventModels = array();

        foreach($events as $event)
        {
            $eventModels[] = new EventModel($this->getContainerInjector(), $event);
        }

        return $eventModels;
    }
}
