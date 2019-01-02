<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\RestBundle\Controller\Events;

use LaDanse\RestBundle\Common\AbstractRestController;
use LaDanse\RestBundle\Common\JsonResponse;
use LaDanse\RestBundle\Common\ResourceHelper;
use LaDanse\ServicesBundle\Common\ServiceException;
use LaDanse\ServicesBundle\Service\DTO\Event\PostEvent;
use LaDanse\ServicesBundle\Service\DTO\Event\PostSignUp;
use LaDanse\ServicesBundle\Service\DTO\Event\PutEvent;
use LaDanse\ServicesBundle\Service\DTO\Event\PutEventState;
use LaDanse\ServicesBundle\Service\DTO\Event\PutSignUp;
use LaDanse\ServicesBundle\Service\Event\EventService;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/")
 */
class EventsResource extends AbstractRestController
{
    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Route("/", name="queryEvents", options = { "expose" = true }, methods={"GET", "HEAD"})
     */
    public function queryEventsAction(Request $request)
    {
        /** @var EventService $eventService */
        $eventService = $this->get(EventService::SERVICE_NAME);

        try
        {
            /** @var \DateTime $startOnDate */
            $startOnDate = $this->getStartOnDate($request->query->get('startOn'));

            $eventPage = $eventService->getAllEventsPaged($startOnDate);

            return new JsonResponse($eventPage);
        }
        catch(ServiceException $serviceException)
        {
            return ResourceHelper::createErrorResponse(
                $request,
                $serviceException->getCode(),
                $serviceException->getMessage()
            );
        }
    }

    /**
     * @param Request $request
     * @param int $eventId
     *
     * @return Response
     *
     * @Route("/{eventId}", name="queryEventById", options = { "expose" = true }, methods={"GET", "HEAD"})
     */
    public function queryEventByIdAction(Request $request, $eventId)
    {
        /** @var EventService $eventService */
        $eventService = $this->get(EventService::SERVICE_NAME);

        try
        {
            $event = $eventService->getEventById($eventId);

            return new JsonResponse($event);
        }
        catch(ServiceException $serviceException)
        {
            return ResourceHelper::createErrorResponse(
                $request,
                $serviceException->getCode(),
                $serviceException->getMessage()
            );
        }
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Route("/", name="postEvent", options = { "expose" = true }, methods={"POST"})
     */
    public function postEventAction(Request $request)
    {
        /** @var EventService $eventService */
        $eventService = $this->get(EventService::SERVICE_NAME);

        try
        {
            /** @var PostEvent $postEventDto */
            $postEventDto = $this->getDtoFromContent($request, PostEvent::class);

            $eventDto = $eventService->postEvent($postEventDto);

            return new JsonResponse(ResourceHelper::object($eventDto));
        }
        catch(ServiceException $serviceException)
        {
            return ResourceHelper::createErrorResponse(
                $request,
                $serviceException->getCode(),
                $serviceException->getMessage()
            );
        }
    }

    /**
     * @param Request $request
     * @param string $eventId
     *
     * @return Response
     *
     * @Route("/{eventId}", name="putEvent", options = { "expose" = true }, methods={"PUT"})
     */
    public function putEventAction(Request $request, $eventId)
    {
        /** @var EventService $eventService */
        $eventService = $this->get(EventService::SERVICE_NAME);

        try
        {
            /** @var PutEvent $putEventDto */
            $putEventDto = $this->getDtoFromContent($request, PutEvent::class);

            $eventDto = $eventService->putEvent(intval($eventId), $putEventDto);

            return new JsonResponse(ResourceHelper::object($eventDto));
        }
        catch(ServiceException $serviceException)
        {
            return ResourceHelper::createErrorResponse(
                $request,
                $serviceException->getCode(),
                $serviceException->getMessage()
            );
        }
    }

    /**
     * @param Request $request
     * @param string $eventId
     *
     * @return Response
     *
     * @Route("/{eventId}/state", name="putEventState", options = { "expose" = true }, methods={"PUT"})
     */
    public function putEventStateAction(Request $request, $eventId)
    {
        /** @var EventService $eventService */
        $eventService = $this->get(EventService::SERVICE_NAME);

        try
        {
            /** @var PutEventState $putEventStateDto */
            $putEventStateDto = $this->getDtoFromContent($request, PutEventState::class);

            $eventDto = $eventService->putEventState(intval($eventId), $putEventStateDto);

            return new JsonResponse(ResourceHelper::object($eventDto));
        }
        catch(ServiceException $serviceException)
        {
            return ResourceHelper::createErrorResponse(
                $request,
                $serviceException->getCode(),
                $serviceException->getMessage()
            );
        }
    }

    /**
     * @param Request $request
     * @param string $eventId
     *
     * @return Response
     *
     * @Route("/{eventId}", name="deleteEvent", options = { "expose" = true }, methods={"DELETE"})
     */
    public function deleteEventAction(Request $request, $eventId)
    {
        /** @var EventService $eventService */
        $eventService = $this->get(EventService::SERVICE_NAME);

        try
        {
            $eventService->deleteEvent(intval($eventId));

            return new Response();
        }
        catch(ServiceException $serviceException)
        {
            return ResourceHelper::createErrorResponse(
                $request,
                $serviceException->getCode(),
                $serviceException->getMessage()
            );
        }
    }

    /**
     * @param Request $request
     * @param string $eventId
     *
     * @return Response
     *
     * @Route("/{eventId}/signUps", name="postSignUp", options = { "expose" = true }, methods={"POST"})
     */
    public function postSignUpAction(Request $request, $eventId)
    {
        /** @var EventService $eventService */
        $eventService = $this->get(EventService::SERVICE_NAME);

        try
        {
            /** @var PostSignUp $postSignUpDto */
            $postSignUpDto = $this->getDtoFromContent($request, PostSignUp::class);

            $eventDto = $eventService->postSignUp(intval($eventId), $postSignUpDto);

            return new JsonResponse(ResourceHelper::object($eventDto));
        }
        catch(ServiceException $serviceException)
        {
            return ResourceHelper::createErrorResponse(
                $request,
                $serviceException->getCode(),
                $serviceException->getMessage()
            );
        }
    }

    /**
     * @param Request $request
     * @param string $eventId
     * @param string $signUpId
     *
     * @return Response
     *
     * @Route("/{eventId}/signUps/{signUpId}", name="putSignUp", options = { "expose" = true }, methods={"PUT"})
     */
    public function putSignUpAction(Request $request, $eventId, $signUpId)
    {
        /** @var EventService $eventService */
        $eventService = $this->get(EventService::SERVICE_NAME);

        try
        {
            /** @var PutSignUp $putSignUpDto */
            $putSignUpDto = $this->getDtoFromContent($request, PutSignUp::class);

            $eventDto = $eventService->putSignUp(intval($eventId), intval($signUpId), $putSignUpDto);

            return new JsonResponse(ResourceHelper::object($eventDto));
        }
        catch(ServiceException $serviceException)
        {
            return ResourceHelper::createErrorResponse(
                $request,
                $serviceException->getCode(),
                $serviceException->getMessage()
            );
        }
    }

    /**
     * @param Request $request
     * @param string $eventId
     * @param string $signUpId
     *
     * @return Response
     *
     * @Route("/{eventId}/signUps/{signUpId}", name="deleteSignUp", options = { "expose" = true }, methods={"DELETE"})
     */
    public function deleteSignUpAction(Request $request, $eventId, $signUpId)
    {
        /** @var EventService $eventService */
        $eventService = $this->get(EventService::SERVICE_NAME);

        try
        {
            $eventDto = $eventService->deleteSignUp(intval($eventId), intval($signUpId));

            return new JsonResponse(ResourceHelper::object($eventDto));
        }
        catch(ServiceException $serviceException)
        {
            return ResourceHelper::createErrorResponse(
                $request,
                $serviceException->getCode(),
                $serviceException->getMessage()
            );
        }
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
