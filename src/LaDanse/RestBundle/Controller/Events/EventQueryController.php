<?php

namespace LaDanse\RestBundle\Controller\Events;

use LaDanse\CommonBundle\Helper\JsonResponse;
use LaDanse\DomainBundle\Entity\Event;
use LaDanse\ServicesBundle\Service\DTO\Event\EventFactory;
use LaDanse\ServicesBundle\Service\Event\EventService;
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

        $result = array();

        $factory = new EventFactory();

        /** @var Event $event */
        foreach($events as $event)
        {
            $result[] = $factory->create($event);
        }

        return new JsonResponse($result);
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
