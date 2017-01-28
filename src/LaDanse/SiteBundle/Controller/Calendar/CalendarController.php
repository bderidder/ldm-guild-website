<?php

namespace LaDanse\SiteBundle\Controller\Calendar;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\Account;
use LaDanse\ServicesBundle\Service\Event\EventService;
use LaDanse\SiteBundle\Common\LaDanseController;
use LaDanse\SiteBundle\Model\EventModel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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


    public function tilePartialAction()
    {
        $startDate = new \DateTime('now');
        $events = $this->getEvents($startDate, $this->getAuthenticationService()->getCurrentContext()->getAccount());

        return $this->render('LaDanseSiteBundle:calendar:calendarTilePartial.html.twig',
                    ['events' => $events]
                );
    }

    protected function getEvents($startDate, Account $currentUser)
    {
        /** @var EventService $eventService */
        $eventService = $this->get(EventService::SERVICE_NAME);

        $events = $eventService->getAllEventsPaged($startDate)->getEvents();

        $eventModels = [];

        foreach($events as $event)
        {
            $eventModels[] = new EventModel($event, $currentUser);
        }

        return $eventModels;
    }
}
