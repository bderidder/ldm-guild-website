<?php

namespace LaDanse\SiteBundle\Controller\Events;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\ServicesBundle\Service\Authorization\NotAuthorizedException;
use LaDanse\ServicesBundle\Service\Event\EventDoesNotExistException;
use LaDanse\ServicesBundle\Service\Event\EventInThePastException;
use LaDanse\ServicesBundle\Service\Event\EventService;
use LaDanse\ServicesBundle\Service\Event\SignUpDoesNotExistException;
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

        try
        {
            $eventService->removeSignUpForAccount($eventId, $account->getId());

            return $this->redirect($this->generateUrl('viewEvent', ['id' => $eventId]));
        }
        catch(EventDoesNotExistException $e)
        {
            $this->logger->warning(
                __CLASS__ . ' the event does not exist',
                [
                    "event"   => $eventId,
                    "account" => $account->getId()
                ]
            );

            return $this->redirect($this->generateUrl('calendarIndex'));
        }
        catch(SignUpDoesNotExistException $e)
        {
            $this->logger->warning(
                __CLASS__ . ' no sign up for this account on given event',
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
                __CLASS__ . ' given event is in the past',
                [
                    "event"   => $eventId,
                    "account" => $account->getId()
                ]
            );

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
    }
}
