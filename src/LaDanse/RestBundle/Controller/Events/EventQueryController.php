<?php

namespace LaDanse\RestBundle\Controller\Events;

use LaDanse\RestBundle\Common\JsonResponse;
use LaDanse\ServicesBundle\Service\Event\EventService;
use LaDanse\ServicesBundle\Service\NewEvent\NewEventService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class EventQueryController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Route("/", name="queryEvents")
     * @Method({"GET","HEAD"})
     */
    public function queryEventsAction(Request $request)
    {
        /** @var EventService $eventService */
        $eventService = $this->get(EventService::SERVICE_NAME);

        /** @var \DateTime $startOnDate */
        $startOnDate = $this->getStartOnDate($request->query->get('startOn'));

        $eventPage = $eventService->getAllEventsPaged($startOnDate);

        $pagedResult = [
            'data'   => $eventPage->getEvents(),
            'paging' => [
                'previous' => $this->generateUrl(
                    'queryEvents',
                    ['startOn' => $eventPage->getPreviousTimestamp()->format('Ymd')],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
                'next'     => $this->generateUrl(
                    'queryEvents',
                    ['startOn' => $eventPage->getNextTimestamp()->format('Ymd')],
                    UrlGeneratorInterface::ABSOLUTE_URL
                )
            ]
        ];

        return new JsonResponse($pagedResult);
    }

    /**
     * @param Request $request
     * @param int $eventId
     *
     * @return Response
     *
     * @Route("/{eventId}", name="queryEventById")
     * @Method({"GET","HEAD"})
     */
    public function queryEventByIdAction(Request $request, $eventId)
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

        return \DateTime::createFromFormat('Ymd', $pStartOnDate);
    }
}
