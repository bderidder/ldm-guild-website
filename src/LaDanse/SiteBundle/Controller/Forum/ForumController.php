<?php

namespace LaDanse\SiteBundle\Controller\Forum;

use LaDanse\CommonBundle\Helper\LaDanseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use JMS\DiExtraBundle\Annotation as DI;

class ForumController extends LaDanseController
{
    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.ladanse")
     */
    private $logger;

    /**
     * @Route("/", name="forumIndex")
     */
    public function indexAction()
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        if (!$authContext->isAuthenticated())
        {
            $this->logger->warning(__CLASS__ . ' the user was not authenticated in forumUnderConstruction');

            return $this->redirect($this->generateUrl('welcomeIndex'));
        }

        return $this->render("LaDanseSiteBundle:forum:forum.html.twig");
    }
}
