<?php

namespace LaDanse\SiteBundle\Controller\Events;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\ServicesBundle\Service\Authorization\NotAuthorizedException;
use LaDanse\ServicesBundle\Service\DTO\Event\Event;
use LaDanse\ServicesBundle\Service\DTO\Event\SignUp;
use LaDanse\ServicesBundle\Service\Event\EventDoesNotExistException;
use LaDanse\ServicesBundle\Service\Event\EventInThePastException;
use LaDanse\ServicesBundle\Service\Event\EventService;
use LaDanse\SiteBundle\Common\LaDanseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/{eventId}/signup")
*/
class RemoveSignUpController extends LaDanseController
{
    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    private $logger;

    /**
     * @param string $eventId
     *
     * @return Response
     *
     * @Route("/remove", name="removeSignUp")
     */
    public function removeAction($eventId)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();
        $account = $authContext->getAccount();

        /** @var EventService $eventService */
        $eventService = $this->get(EventService::SERVICE_NAME);

        /** @var Event $event */
        $event = null;

        try
        {
            $event = $eventService->getEventById($eventId);
        }
        catch(EventDoesNotExistException $e)
        {
            $this->logger->warning(
                __CLASS__ . ' the event does not exist in indexAction',
                [
                    "event" => $eventId
                ]
            );

            return $this->redirect($this->generateUrl('calendarIndex'));
        }

        /** @var SignUp $currentSignUp */
        $currentSignUp = null;

        /** @var SignUp $signUp */
        foreach($event->getSignUps() as $signUp)
        {
            if ($signUp->getAccount()->getId() == $account->getId())
            {
                $currentSignUp = $signUp;
            }
        }

        if (is_null($currentSignUp))
        {
            $this->logger->warning(
                __CLASS__ . ' the user is not yet subscribed to this event in editSignUp',
                [
                    'event' => $eventId,
                    'user' => $this->getAccount()->getId()
                ]
            );

            return $this->redirect($this->generateUrl('viewEvent', ['id' => $eventId]));
        }

        try
        {
            $eventService->deleteSignUp($eventId, $currentSignUp->getId());

            return $this->redirect($this->generateUrl('viewEvent', ['id' => $eventId]));
        }
        catch(NotAuthorizedException $e)
        {
            $this->logger->warning(
                __CLASS__ . ' currently logged in user is not allowed to remove this sign up',
                [
                    "event"   => $eventId,
                    "account" => $account->getId()
                ]
            );

            return $this->redirect($this->generateUrl('viewEvent', ['id' => $eventId]));
        }
        catch(EventInThePastException $e)
        {
            $this->logger->warning(
                __CLASS__ . ' event is in the past, cannot remove sign-up',
                [
                    "event"     => $eventId,
                    "account"   => $account->getId()
                ]
            );

            return $this->redirect($this->generateUrl('viewEvent', ['id' => $eventId]));
        }
        catch(\Exception $e)
        {
            $this->logger->warning(
                __CLASS__ . ' unknown error occured while attempting to delete sign-up',
                [
                    "exception" => $e->getMessage(),
                    "event"     => $eventId,
                    "account"   => $account->getId()
                ]
            );

            return $this->redirect($this->generateUrl('calendarIndex'));
        }
    }
}
