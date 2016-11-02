<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\SiteBundle\Controller\Angular;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Service\Forum\ForumStatsService;

use LaDanse\SiteBundle\Common\LaDanseController;
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
     * @Route("/forum", name="forumIndex")
     */
    public function indexAction()
    {
        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::FORUM_VIEW,
                $this->getAuthenticationService()->getCurrentContext()->getAccount())
        );

        return $this->render(
            'LaDanseSiteBundle:angular:angular.html.twig',
            [
                'pageTitle' => 'Forum'
            ]
        );
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
            [
                'unreadPosts' => $unreadPosts
            ]
        );
    }
}
