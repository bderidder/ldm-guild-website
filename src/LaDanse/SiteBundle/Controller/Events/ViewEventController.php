<?php

namespace LaDanse\SiteBundle\Controller\Events;

use LaDanse\CommonBundle\Helper\LaDanseController;
use LaDanse\ServicesBundle\Service\Event\EventService;
use LaDanse\SiteBundle\Model\EventModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;

use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;

use JMS\DiExtraBundle\Annotation as DI;

class ViewEventController extends LaDanseController
{
	const EVENT_REPOSITORY = 'LaDanseDomainBundle:Event';

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

        $event = $eventService->getEventById($id);

        if (null === $event)
        {
            $this->logger->warning(
                __CLASS__ . ' the event does not exist in indexAction',
                array("event" => $id)
            );

            return $this->redirect($this->generateUrl('calendarIndex'));
        }
        else
        {
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
                array(
                    'isFuture' => ($event->getInviteTime() > $currentDateTime),
                    'event' => new EventModel($this->getContainerInjector(), $event, $this->getAccount()))
            );
        }
    }
}
