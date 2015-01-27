<?php

namespace LaDanse\SiteBundle\Controller\Privacy;

use LaDanse\CommonBundle\Helper\LaDanseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use JMS\DiExtraBundle\Annotation as DI;

class PrivacyPolicyController extends LaDanseController
{
    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    private $logger;

	/**
     * @Route("/", name="privacyPolicyIndex")
     */
    public function indexAction()
    {
        return $this->render("LaDanseSiteBundle:privacy:privacyPolicy.html.twig");
    }
}
