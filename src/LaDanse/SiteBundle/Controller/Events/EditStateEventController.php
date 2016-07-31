<?php

namespace LaDanse\SiteBundle\Controller\Events;

use DateTime;
use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\Event;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Service\Authorization\NotAuthorizedException;
use LaDanse\ServicesBundle\Service\Authorization\ResourceByValue;
use LaDanse\ServicesBundle\Service\Authorization\SubjectReference;
use LaDanse\ServicesBundle\Service\Event\EventDoesNotExistException;
use LaDanse\ServicesBundle\Service\Event\EventInvalidStateChangeException;
use LaDanse\ServicesBundle\Service\Event\EventService;
use LaDanse\SiteBundle\Common\LaDanseController;
use LaDanse\SiteBundle\Form\Model\EventFormModel;
use LaDanse\SiteBundle\Form\Type\EventFormType;
use LaDanse\SiteBundle\Model\ErrorModel;
use LaDanse\SiteBundle\Model\EventModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

class EditStateEventController extends LaDanseController
{
	/**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    private $logger;

	/**
     * @param $request Request
     * @param $id string
     *
     * @return Response
     *
     * @Route("/{id}/state/confirm", name="confirmEvent")
     */
    public function confirmEventAction(Request $request, $id)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();
        $account = $authContext->getAccount();

        /** @var EventService $eventService */
        $eventService = $this->get(EventService::SERVICE_NAME);

        try
        {
            $eventService->confirmEvent($id);

            return $this->redirect($this->generateUrl('viewEvent', array('id' => $id)));
        }
        catch(EventDoesNotExistException $e)
        {
            $this->logger->warning(
                __CLASS__ . ' the event does not exist in confirmEvent',
                array("event" => $id)
            );

            return $this->redirect($this->generateUrl('calendarIndex'));
        }
        catch(NotAuthorizedException $e)
        {
            $this->logger->warning(__CLASS__ . ' the user is not authorized to confirm event');

            return $this->redirect($this->generateUrl('viewEvent', array('id' => $id)));
        }
        catch(EventInvalidStateChangeException $e)
        {
            $this->logger->warning(__CLASS__ . ' the event is in a state that does not allow confirmation');

            return $this->redirect($this->generateUrl('viewEvent', array('id' => $id)));
        }
        catch(\Throwable $t)
        {
            $this->logger->warning(
                __CLASS__ . ' unexpected error',
                array(
                    "throwable" => $t,
                    "event"     => $id,
                    "account"   => $account->getId()
                )
            );

            return $this->redirect($this->generateUrl('viewEvent', array('id' => $id)));
        }
    }

    /**
     * @param $request Request
     * @param $id string
     *
     * @return Response
     *
     * @Route("/{id}/state/cancel", name="cancelEvent")
     */
    public function cancelEventAction(Request $request, $id)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();
        $account = $authContext->getAccount();

        /** @var EventService $eventService */
        $eventService = $this->get(EventService::SERVICE_NAME);

        try
        {
            $eventService->cancelEvent($id);

            return $this->redirect($this->generateUrl('viewEvent', array('id' => $id)));
        }
        catch(EventDoesNotExistException $e)
        {
            $this->logger->warning(
                __CLASS__ . ' the event does not exist in cancelEvent',
                array("event" => $id)
            );

            return $this->redirect($this->generateUrl('calendarIndex'));
        }
        catch(NotAuthorizedException $e)
        {
            $this->logger->warning(__CLASS__ . ' the user is not authorized to cancel event');

            return $this->redirect($this->generateUrl('viewEvent', array('id' => $id)));
        }
        catch(EventInvalidStateChangeException $e)
        {
            $this->logger->warning(__CLASS__ . ' the event is in a state that does not allow cancellation');

            return $this->redirect($this->generateUrl('viewEvent', array('id' => $id)));
        }
        catch(\Exception $e)
        {
            $this->logger->warning(
                __CLASS__ . ' unexpected error',
                array(
                    "throwable" => $e,
                    "event"     => $id,
                    "account"   => $account->getId()
                )
            );

            return $this->redirect($this->generateUrl('viewEvent', array('id' => $id)));
        }
    }
}
