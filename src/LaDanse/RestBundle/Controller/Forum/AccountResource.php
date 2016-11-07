<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\RestBundle\Controller\Forum;

use LaDanse\RestBundle\Common\AbstractRestController;
use LaDanse\ServicesBundle\Service\Forum\ForumStatsService;
use LaDanse\SiteBundle\Security\AuthenticationService;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * @Route("/account")
 */
class AccountResource extends AbstractRestController
{
    /**
     * @DI\Inject("monolog.logger.ladanse")
     * @var LoggerInterface $logger
     */
    private $logger;

    /**
     * @return Response
     *
     * @Route("/unread", name="getUnreadForAccount")
     * @Method({"GET"})
     */
    public function getUnreadForAccountAction()
    {
        /** @var AuthenticationService $authenticationService */
        $authenticationService = $this->get(AuthenticationService::SERVICE_NAME);
        $authContext = $authenticationService->getCurrentContext();

        if (!$authContext->isAuthenticated())
        {
            $this->logger->warning(__CLASS__ . ' the user was not authenticated in getUnreadForAccount');

            $jsonObject = (object)[
                "status" => "must be authenticated"
            ];

            return new JsonResponse($jsonObject);
        }

        $account = $authContext->getAccount();

        /** @var ForumStatsService $statsService */
        $statsService = $this->get(ForumStatsService::SERVICE_NAME);

        $unreadPosts = $statsService->getUnreadPostsForAccount($account);

        $postMapper = new PostMapper();

        $jsonObject = (object)[
            "accountId"   => $account->getId(),
            "displayName" => $account->getDisplayName(),
            "unreadPosts" => $postMapper->mapPostsAndTopic($this->get('router'), $unreadPosts),
            "links"       => (object)[
                "self"  => $this->generateUrl('getUnreadForAccount', [], true)
            ]
        ];

        return new JsonResponse($jsonObject);
    }
}
