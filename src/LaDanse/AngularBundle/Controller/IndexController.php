<?php

namespace LaDanse\AngularBundle\Controller;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\SiteBundle\Common\LaDanseController;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Symfony\Component\HttpFoundation\Response;

class IndexController extends LaDanseController
{
    /**
     * @var $eventDispatcher EventDispatcherInterface
     * @DI\Inject("event_dispatcher")
     */
    private $eventDispatcher;

	/**
     * @return Response
     *
     * @Route("/", name="angularIndex")
     */
    public function indexAction()
    {
        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::ANGULAR_VIEW,
                null
            )
        );

        return $this->render("LaDanseAngularBundle::index.html.twig");
    }
}