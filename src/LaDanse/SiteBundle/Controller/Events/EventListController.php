<?php

namespace LaDanse\SiteBundle\Controller\Events;

use LaDanse\CommonBundle\Helper\LaDanseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use JMS\DiExtraBundle\Annotation as DI;

class EventListController extends LaDanseController
{
    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    private $logger;

	/**
     * @Route("/", name="eventList")
     */
    public function viewAction()
    {
        return $this->render('LaDanseSiteBundle:events:eventList.html.twig');
    }
}
