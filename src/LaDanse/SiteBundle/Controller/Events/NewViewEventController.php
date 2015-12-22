<?php

namespace LaDanse\SiteBundle\Controller\Events;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\CommonBundle\Helper\JsonResponse;
use LaDanse\CommonBundle\Helper\LaDanseController;
use LaDanse\ServicesBundle\Service\Event\EventDoesNotExistException;
use LaDanse\ServicesBundle\Service\DTO as DTO;
use LaDanse\ServicesBundle\Service\NewEvent\NewEventService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class NewViewEventController extends LaDanseController
{
	/**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    private $logger;

	/**
     * @param string $id
     *
     * @return Response
     *
     * @Route("/new/{id}", name="viewNewEvent")
     */
    public function viewAction($id)
    {
        /** @var NewEventService $eventService */
        $eventService = $this->get(NewEventService::SERVICE_NAME);

        try
        {
            /** @var DTO\Event\Event $event */
            $event = $eventService->getEventById($id);

            return new JsonResponse($event);
        }
        catch(EventDoesNotExistException $e)
        {
            $this->logger->warning(
                __CLASS__ . ' the event does not exist in indexAction',
                array("event" => $id)
            );

            return $this->redirect($this->generateUrl('calendarIndex'));
        }
    }
}
