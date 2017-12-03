<?php

namespace LaDanse\SiteBundle\Controller\ChatVoice;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;

use LaDanse\SiteBundle\Common\LaDanseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;

class ChatVoiceController extends LaDanseController
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
     * @Route("/", name="chatVoiceIndex")
     */
    public function indexAction()
    {
    	$authContext = $this->getAuthenticationService()->getCurrentContext();
    	
    	if (!$authContext->isAuthenticated())
        {
            $this->logger->warning(__CLASS__ . ' the user was not authenticated in indexAction');

            return $this->redirect($this->generateUrl('welcomeIndex'));
        }

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::CHATVOICE_VIEW,
                $this->getAuthenticationService()->getCurrentContext()->getAccount())
        );

        return $this->render('LaDanseSiteBundle:chatvoice:index.html.twig');
    }

    /**
     * @return Response
     *
     * @Route("/bothelp", name="botHelpIndex")
     */
    public function botHelpAction()
    {
        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::CHATVOICE_BOTHELP,
                $this->getAuthenticationService()->getCurrentContext()->getAccount())
        );

        return $this->render('LaDanseSiteBundle:chatvoice:bothelp.html.twig');
    }
}
