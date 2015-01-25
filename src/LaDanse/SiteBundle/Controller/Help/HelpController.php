<?php

namespace LaDanse\SiteBundle\Controller\Help;

use LaDanse\CommonBundle\Helper\LaDanseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use JMS\DiExtraBundle\Annotation as DI;

class HelpController extends LaDanseController
{
    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.latte")
     */
    private $logger;

	/**
     * @Route("/", name="helpIndex")
     */
    public function indexAction()
    {
        return $this->render("LaDanseSiteBundle:help:index.html.twig");
    }
}
