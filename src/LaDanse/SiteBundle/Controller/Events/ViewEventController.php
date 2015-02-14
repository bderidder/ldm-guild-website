<?php

namespace LaDanse\SiteBundle\Controller\Events;

use LaDanse\CommonBundle\Helper\LaDanseController;
use LaDanse\ServicesBundle\EventListener\Features;
use LaDanse\SiteBundle\Model\EventModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use LaDanse\ServicesBundle\EventListener\FeatureUseEvent;

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
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        $currentDateTime = new \DateTime();

        if (!$authContext->isAuthenticated())
        {
            $this->logger->warning(__CLASS__ . ' the user was not authenticated in indexAction');

            return $this->redirect($this->generateUrl('welcomeIndex'));
        }

        $em = $this->getDoctrine();
        /* @var $repository \Doctrine\ORM\EntityRepository */
        $repository = $em->getRepository(self::EVENT_REPOSITORY);
        /* @var $event \LaDanse\DomainBundle\Entity\Event */
        $event = $repository->find($id);

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
                FeatureUseEvent::EVENT_NAME,
                new FeatureUseEvent(
                    Features::EVENT_VIEW,
                    $this->getAuthenticationService()->getCurrentContext()->getAccount()
                )
            );

            return $this->render(
                'LaDanseSiteBundle:events:viewEvent.html.twig',
                array(
                    'isFuture' => ($event->getInviteTime() > $currentDateTime),
                    'event' => new EventModel($this->getContainerInjector(), $event, $authContext->getAccount()))
            );
        }
    }
}
