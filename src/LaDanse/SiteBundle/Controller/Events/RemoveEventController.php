<?php

namespace LaDanse\SiteBundle\Controller\Events;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\ServicesBundle\Service\Event\EventDoesNotExistException;
use LaDanse\ServicesBundle\Service\Event\EventInThePastException;
use LaDanse\ServicesBundle\Service\Event\EventService;
use LaDanse\SiteBundle\Common\LaDanseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class RemoveEventController
 *
 * @package LaDanse\SiteBundle\Controller\Events
 */
class RemoveEventController extends LaDanseController
{
    const EVENT_REPOSITORY = 'LaDanseDomainBundle:Event';

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
     * @Route("/{id}/remove", name="removeEvent")
     */
    public function removeAction($id)
    {
        /** @var $eventService EventService */
        $eventService = $this->get(EventService::SERVICE_NAME);

        try
        {
            $eventService->removeEvent($id);

            $this->addToast('Event removed');

            return $this->redirect($this->generateUrl('menuIndex'));
        }
        catch (EventDoesNotExistException $e)
        {
            $this->logger->warning(
                __CLASS__ . ' the event does not exist in removeAction',
                array("event id" => $id)
            );

            return $this->redirect($this->generateUrl('calendarIndex'));
        }
        catch(EventInThePastException $e)
        {
            $this->logger->warning(
                __CLASS__ . ' the event is in the past in removeAction',
                array("event id" => $id)
            );

            return $this->redirect($this->generateUrl('viewEvent', array('id' => $id)));
        }
    }
}
