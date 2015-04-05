<?php

namespace LaDanse\SiteBundle\Controller\Events;

use LaDanse\CommonBundle\Helper\LaDanseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;

use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;

use JMS\DiExtraBundle\Annotation as DI;

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
     * @var $eventDispatcher EventDispatcherInterface
     * @DI\Inject("event_dispatcher")
     */
    private $eventDispatcher;

    /**
     * @param string $id
     *
     * @return Response
     *
     * @Route("/{id}/remove", name="removeEvent")
     */
    public function removeAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository(self::EVENT_REPOSITORY);

        /* @var $repository \Doctrine\ORM\EntityRepository */
        $event = $repository->find($id);

        if (null === $event)
        {
            $this->logger->warning(
                __CLASS__ . ' the event does not exist in deleteAction',
                array("event" => $id)
            );

            return $this->redirect($this->generateUrl('calendarIndex'));
        }
        else
        {
            $currentDateTime = new \DateTime();
            if ($event->getInviteTime() <= $currentDateTime)
            {
                return $this->redirect($this->generateUrl('viewEvent', array('id' => $id)));
            }

            $this->getCommentService()->removeCommentGroup($event->getTopicId());

            $em->remove($event);

            $this->logger->warning(
                __CLASS__ . ' removing event in deleteAction',
                array("event" => $id)
            );

            $em->flush();

            $this->addToast('Event removed');

            $this->eventDispatcher->dispatch(
                ActivityEvent::EVENT_NAME,
                new ActivityEvent(
                    ActivityType::EVENT_DELETE,
                    $this->getAuthenticationService()->getCurrentContext()->getAccount(),
                    array(
                        'event' => $event->toJson()
                    )
                )
            );

            return $this->redirect($this->generateUrl('menuIndex'));
        }
    }
}
