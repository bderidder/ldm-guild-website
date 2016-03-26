<?php

namespace LaDanse\RestBundle\Controller\Events;

use LaDanse\CommonBundle\Helper\JsonResponse;
use LaDanse\DomainBundle\Entity\Event;
use LaDanse\ServicesBundle\DTO\Events\EventDto;
use LaDanse\ServicesBundle\Service\Event\EventService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

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

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);

        $events = $eventService->getAllEvents();

        $result = array();

        /** @var Event $event */
        foreach($events as $event)
        {
            $eventDto = new EventDto();

            $eventDto->setName($event->getName())
                ->setDescription($event->getDescription());

            $result[] = $eventDto;
        }

        $jsonContent = $serializer->serialize($result, 'json');

        return new JsonResponse($result);
    }

    private function getStartOnDate($pStartOnDate)
    {
        if ($pStartOnDate == null)
        {
            return new \DateTime();
        }


    }
}
