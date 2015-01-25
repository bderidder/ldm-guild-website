<?php

namespace LaDanse\SiteBundle\Controller\Gallery;

use LaDanse\CommonBundle\Helper\LaDanseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use JMS\DiExtraBundle\Annotation as DI;

class UnderConstructionController extends LaDanseController
{
    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.latte")
     */
    private $logger;

	/**
     * @Route("/", name="galleryUnderConstruction")
     */
    public function indexAction()
    {
        return $this->render("LaDanseSiteBundle:gallery:underConstruction.html.twig");
    }
}
