<?php

namespace LaDanse\RestBundle\Controller\Events;

use LaDanse\DomainBundle\Entity\Event;
use LaDanse\RestBundle\Common\JsonResponse;
use LaDanse\ServicesBundle\Service\DTO\Event\EventFactory;
use LaDanse\ServicesBundle\Service\Event\EventService;
use LaDanse\ServicesBundle\Service\NewEvent\NewEventService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EventQueryController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Route("/")
     * @Method({"GET","HEAD"})
     */
    public function queryEvents(Request $request)
    {
        /** @var EventService $eventService */
        $eventService = $this->get(EventService::SERVICE_NAME);

        /** @var \DateTime $startOnDate */
        $startOnDate = $this->getStartOnDate($request->query->get('startOn'));

        $events = $eventService->getAllEvents();

        $result = [];

        /** @var Event $event */
        foreach($events as $event)
        {
            $result[] = EventFactory::create($event);
        }

        return new JsonResponse($result);
    }

    /**
     * @param Request $request
     * @param int $eventId
     *
     * @return Response
     *
     * @Route("/{eventId}")
     * @Method({"GET","HEAD"})
     */
    public function queryEventById(Request $request, $eventId)
    {
        /** @var NewEventService $eventService */
        $eventService = $this->get(NewEventService::SERVICE_NAME);

        $event = $eventService->getEventById($eventId);

        return new JsonResponse($event);
    }

    private function getStartOnDate($pStartOnDate)
    {
        if ($pStartOnDate == null)
        {
            return new \DateTime();
        }

        return $pStartOnDate;
    }
}
