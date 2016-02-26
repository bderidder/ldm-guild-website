<?php

namespace LaDanse\SiteBundle\Controller\Menu;

use Doctrine\Common\Cache\Cache;
use LaDanse\CommonBundle\Helper\LaDanseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;

use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;

use JMS\DiExtraBundle\Annotation as DI;

class MenuController extends LaDanseController
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
     * @Route("/", name="menuIndex")
     */
    public function indexAction()
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        if (!$authContext->isAuthenticated())
        {
            $this->logger->warning(__CLASS__ . ' the user was not authenticated in menuIndex');

            return $this->redirect($this->generateUrl('welcomeIndex'));
        }

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::MENU_VIEW,
                $this->getAuthenticationService()->getCurrentContext()->getAccount())
        );

        /** @var Cache $cacheService */
        $cacheService = $this->get('cache');

        $items = $cacheService->fetch('wowhead.rss');

        return $this->render(
            'LaDanseSiteBundle:menu:menu.html.twig',
            array(
                "wowheadNews" => $items
            )
        );
    }
}
