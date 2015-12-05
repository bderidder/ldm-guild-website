<?php

namespace LaDanse\SiteBundle\Controller\Help;

use LaDanse\CommonBundle\Helper\LaDanseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpFoundation\Response;

class HelpController extends LaDanseController
{
    /**
     * @var $eventDispatcher EventDispatcherInterface
     * @DI\Inject("event_dispatcher")
     */
    private $eventDispatcher;

	/**
     * @Route("/", name="helpIndex")
     *
     * @return Response
     */
    public function indexAction()
    {
        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::HELP_VIEW,
                $this->getAuthenticationService()->getCurrentContext()->getAccount())
        );

        return $this->render("LaDanseSiteBundle:help:index.html.twig");
    }
}
