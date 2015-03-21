<?php

namespace LaDanse\SiteBundle\Controller\Privacy;

use LaDanse\CommonBundle\Helper\LaDanseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;

use JMS\DiExtraBundle\Annotation as DI;

class PrivacyPolicyController extends LaDanseController
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
     * @Route("/", name="privacyPolicyIndex")
     */
    public function indexAction()
    {
        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::PRIVACY_VIEW,
                $this->getAuthenticationService()->getCurrentContext()->isAuthenticated() ? $this->getAuthenticationService()->getCurrentContext()->getAccount() : null
            )
        );

        return $this->render("LaDanseSiteBundle:privacy:privacyPolicy.html.twig");
    }
}
