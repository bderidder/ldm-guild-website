<?php

namespace LaDanse\SiteBundle\Controller\ActivityStream;

use LaDanse\CommonBundle\Helper\LaDanseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;

use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;

use JMS\DiExtraBundle\Annotation as DI;

class ActivityStreamController extends LaDanseController
{
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
     * @return Response
     *
     * @Route("/", name="streamIndex")
     */
    public function indexAction()
    {
        if ($this->getAuthenticationService()->getCurrentContext()->isAuthenticated())
        {
            $account = $this->getAuthenticationService()->getCurrentContext()->getAccount();
        }
        else
        {
            $account = null;
        }

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::ABOUT_VIEW,
                $account
            )
        );

        return $this->render("LaDanseSiteBundle:about:about.html.twig");
    }
}
