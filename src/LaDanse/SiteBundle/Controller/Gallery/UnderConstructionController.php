<?php

namespace LaDanse\SiteBundle\Controller\Gallery;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\SiteBundle\Common\LaDanseController;
use LaDanse\ServicesBundle\Activity\ActivityEvent;

use LaDanse\ServicesBundle\Activity\ActivityType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;

class UnderConstructionController extends LaDanseController
{
    /**
     * @var $eventDispatcher EventDispatcherInterface
     * @DI\Inject("event_dispatcher")
     */
    private $eventDispatcher;

	/**
     * @Route("/", name="galleryUnderConstruction")
     *
     * @return Response
     */
    public function indexAction()
    {
        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::GALLERY_VIEW,
                $this->getAuthenticationService()->getCurrentContext()->getAccount())
        );

        return $this->render("LaDanseSiteBundle:gallery:underConstruction.html.twig");
    }
}
