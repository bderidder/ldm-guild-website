<?php

namespace LaDanse\SiteBundle\Controller\Calendar;

use LaDanse\CommonBundle\Helper\LaDanseController;
use LaDanse\DomainBundle\Entity\CalendarExport;
use LaDanse\ServicesBundle\EventListener\Features;
use LaDanse\SiteBundle\Model\EventModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use LaDanse\DomainBundle\Entity\Account;
use LaDanse\ServicesBundle\EventListener\FeatureUseEvent;

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
     * @var $eventDispatcher EventDispatcherInterface
     * @DI\Inject("event_dispatcher")
     */
    private $eventDispatcher;

    /**
     * @param $secret string
     *
     * @return Response
     *
     * @Route("/ical/{secret}", name="icalIndex")
     */
    public function indexAction($secret)
    {
        $exportSettings = $this->getExportSettings($secret);

        if ($exportSettings === null)
        {
            $this->logger->info(__CLASS__ . " Supplied secret is not known", array("secret" => $secret));

            return new Response('', Response::HTTP_NOT_FOUND);
        }

        $this->logger->info(
            __CLASS__ . " found account for secret ",
            array(
                "account" => $exportSettings->getAccount()->getUsername()
            )
        );

        $account = $exportSettings->getAccount();

        $this->eventDispatcher->dispatch(
            FeatureUseEvent::EVENT_NAME,
            new FeatureUseEvent(Features::CALENDAR_ICAL, $account)
        );

        $vCalendar = new iCal\Calendar('www.ladanse.org');

        // we suggest a refresh every 30 minutes
        $vCalendar->setPublishedTTL('P30M');

        $allEvents = $this->getAllEvents($account);

        /** @var \LaDanse\SiteBundle\Model\EventModel $event */
        foreach($allEvents as $event)
        {
            if ($event->getSignUps()->getCurrentUserWillCome() || $event->getSignUps()->getCurrentUserMightCome())
            {
                $vEvent = $this->createICalEvent($event, '(SIGNED) ' . $event->getName());
                $vEvent->setStatus(iCal\Event::STATUS_CONFIRMED);

                $vCalendar->addComponent($vEvent);
            }

            if ($event->getSignUps()->getCurrentUserAbsent() && $exportSettings->getExportAbsence())
            {
                $vEvent = $this->createICalEvent($event, '(ABSENT) ' . $event->getName());
                $vEvent->setStatus(iCal\Event::STATUS_TENTATIVE); // CANCELLED is not always shown in some applications

                $vCalendar->addComponent($vEvent);
            }

            if (!($event->getSignUps()->getCurrentUserSignedUp() || $event->getSignUps()->getCurrentUserAbsent())
                && $exportSettings->getExportNew())
            {
                $vEvent = $this->createICalEvent($event, '(NEW) ' . $event->getName());
                $vEvent->setStatus(iCal\Event::STATUS_TENTATIVE);

                $vCalendar->addComponent($vEvent);
            }
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

    protected function getAllEvents(Account $currentUser)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->select('e')
            ->from('LaDanse\DomainBundle\Entity\Event', 'e')
            ->orderBy('e.inviteTime', 'ASC');

        $this->logger->debug(
            __CLASS__ . " created DQL for retrieving Events ",
            array(
                "query" => $qb->getDQL()
            )
        );

        /* @var $query \Doctrine\ORM\Query */
        $query = $qb->getQuery();

        $events = $query->getResult();

        $eventModels = array();

        foreach($events as $event)
        {
            $eventModels[] = new EventModel($this->getContainerInjector(), $event, $currentUser);
        }

        return $eventModels;
    }

    /**
     * @param $secret string
     *
     * @return CalendarExport
     */
    protected function getExportSettings($secret)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->select('s')
            ->from('LaDanse\DomainBundle\Entity\CalendarExport', 's')
            ->leftJoin('s.account', 'a')
            ->where($qb->expr()->eq('s.secret', '?1'))
            ->setParameter(1, $secret);

        $this->logger->debug(
            __CLASS__ . " created DQL for retrieving CalendarExport ",
            array(
                "query" => $qb->getDQL()
            )
        );

        /* @var $query \Doctrine\ORM\Query */
        $query = $qb->getQuery();

        $result = $query->getResult();

        if (count($result) != 1)
        {
            return null;
        }
        else
        {
            return $result[0];
        }
    }

    /**
     * @param $event \LaDanse\SiteBundle\Model\EventModel
     * @param $description string
     *
     * @return iCal\Event
     */
    protected function createICalEvent($event, $description)
    {
        $vEvent = new iCal\Event();
        $vEvent->setDtStart($event->getInviteTime());
        $vEvent->setDtEnd($event->getEndTime());
        $vEvent->setSummary($description);
        $vEvent->setUrl($this->generateUrl('viewEvent', array('id' => $event->getId()), true));

        $vEvent->setUseTimezone(true);

        return $vEvent;
    }
}