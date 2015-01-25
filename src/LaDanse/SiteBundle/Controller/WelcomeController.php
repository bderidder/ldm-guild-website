<?php

namespace LaDanse\SiteBundle\Controller;

use LaDanse\CommonBundle\Helper\LaDanseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * @Route("/")
*/
class WelcomeController extends LaDanseController
{
    /**
     * @var $logger \Monolog\Logger
     * @DI\Inject("monolog.logger.latte")
     */
    private $logger;

	/**
     * @return Response
     *
     * @Route("/", name="welcomeIndex")
     */
    public function indexAction()
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        if (!$authContext->isAuthenticated())
        {
            return $this->render("LaDanseSiteBundle::welcome.html.twig");
        }
        else
        {
            return $this->redirect($this->generateUrl('menuIndex'));
        }
    }
}
