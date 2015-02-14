<?php

namespace LaDanse\SiteBundle\Controller\Help;

use LaDanse\CommonBundle\Helper\LaDanseController;
use LaDanse\ServicesBundle\EventListener\Features;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use LaDanse\ServicesBundle\EventListener\FeatureUseEvent;

use JMS\DiExtraBundle\Annotation as DI;

class HelpController extends LaDanseController
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
     * @Route("/", name="helpIndex")
     */
    public function indexAction()
    {
        $this->eventDispatcher->dispatch(
            FeatureUseEvent::EVENT_NAME,
            new FeatureUseEvent(
                Features::HELP_VIEW,
                $this->getAuthenticationService()->getCurrentContext()->getAccount()
            )
        );

        return $this->render("LaDanseSiteBundle:help:index.html.twig");
    }
}
