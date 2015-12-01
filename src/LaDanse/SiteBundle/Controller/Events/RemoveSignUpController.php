<?php

namespace LaDanse\SiteBundle\Controller\Events;

use LaDanse\CommonBundle\Helper\LaDanseController;
use LaDanse\ServicesBundle\Service\Event\Command\EventDoesNotExistException;
use LaDanse\ServicesBundle\Service\Event\Command\EventInThePastException;
use LaDanse\ServicesBundle\Service\Event\Command\SignUpDoesNotExistException;
use LaDanse\ServicesBundle\Service\Event\EventService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * @Route("/{eventId}/signup")
*/
class RemoveSignUpController extends LaDanseController
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

            return $this->redirect($this->generateUrl('viewEvent', array('id' => $eventId)));
        }
        catch(EventDoesNotExistException $e)
        {
            $this->logger->warning(
                __CLASS__ . ' the event does not exist',
                array(
                    "event"   => $eventId,
                    "account" => $account->getId()
                )
            );

            return $this->redirect($this->generateUrl('calendarIndex'));
        }
        catch(SignUpDoesNotExistException $e)
        {
            $this->logger->warning(
                __CLASS__ . ' no sign up for this account on given event',
                array(
                    "event"   => $eventId,
                    "account" => $account->getId()
                )
            );

            return $this->redirect($this->generateUrl('viewEvent', array('id' => $eventId)));
        }
        catch(EventInThePastException $e)
        {
            $this->logger->warning(
                __CLASS__ . ' given event is in the past',
                array(
                    "event"   => $eventId,
                    "account" => $account->getId()
                )
            );

            return $this->redirect($this->generateUrl('viewEvent', array('id' => $eventId)));
        }
    }
}
