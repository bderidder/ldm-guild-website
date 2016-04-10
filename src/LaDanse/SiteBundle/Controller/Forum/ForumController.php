<?php

namespace LaDanse\SiteBundle\Controller\Forum;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\SiteBundle\Common\LaDanseController;
use LaDanse\ForumBundle\Service\ForumStatsService;
use LaDanse\ServicesBundle\Activity\ActivityEvent;

use LaDanse\ServicesBundle\Activity\ActivityType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ForumController extends LaDanseController
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

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::FORUM_VIEW,
                $this->getAuthenticationService()->getCurrentContext()->getAccount())
        );

        return $this->render("LaDanseSiteBundle:forum:forum.html.twig");
    }

    public function tileLabelAction()
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        if (!$authContext->isAuthenticated())
        {
            $this->logger->warning(__CLASS__ . ' the user was not authenticated in tileLabelAction');

            return "";
        }

        $account = $authContext->getAccount();

        /** @var ForumStatsService $statsService */
        $statsService = $this->get(ForumStatsService::SERVICE_NAME);

        $unreadPosts = $statsService->getUnreadPostsForAccount($account);

        return $this->render(
            "LaDanseSiteBundle:forum:menuPartial.html.twig",
            array(
                'unreadPosts' => $unreadPosts
            )
        );
    }
}
