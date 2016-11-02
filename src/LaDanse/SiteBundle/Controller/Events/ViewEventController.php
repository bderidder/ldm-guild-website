<?php

namespace LaDanse\SiteBundle\Controller\Events;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Service\Event\EventDoesNotExistException;
use LaDanse\ServicesBundle\Service\Event\EventService;
use LaDanse\SiteBundle\Common\LaDanseController;
use LaDanse\SiteBundle\Model\EventModel;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Symfony\Component\HttpFoundation\Response;

class ViewEventController extends LaDanseController
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
     * @param string $id
     *
     * @return Response
     *
     * @Route("/{id}", name="viewEvent")
     */
    public function viewAction($id)
    {
        $currentDateTime = new \DateTime();

        /** @var EventService $eventService */
        $eventService = $this->get(EventService::SERVICE_NAME);

        try
        {
            $event = $eventService->getEventById($id);
        }
        catch(EventDoesNotExistException $e)
        {
            $this->logger->warning(
                __CLASS__ . ' the event does not exist in indexAction',
                ["event" => $id]
            );

            return $this->redirect($this->generateUrl('calendarIndex'));
        }

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::EVENT_VIEW,
                $this->getAuthenticationService()->getCurrentContext()->getAccount(),
                $event->toJson()
            )
        );

        return $this->render(
            'LaDanseSiteBundle:events:viewEvent.html.twig',
            [
                'isFuture' => ($event->getInviteTime() > $currentDateTime),
                'event'    => new EventModel($event, $this->getAccount())
            ]
        );
    }
}
