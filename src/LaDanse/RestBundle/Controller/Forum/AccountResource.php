<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\RestBundle\Controller\Forum;

use LaDanse\RestBundle\Controller\Forum\PostMapper;
use LaDanse\RestBundle\Common\AbstractRestController;
use LaDanse\ServicesBundle\Service\Forum\ForumStatsService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/account")
 */
class AccountResource extends AbstractRestController
{
    /**
     * @return Response
     *
     * @Route("/unread", name="getUnreadForAccount")
     * @Method({"GET"})
     */
    public function getUnreadForAccount()
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        if (!$authContext->isAuthenticated())
        {
            $this->getLogger()->warning(__CLASS__ . ' the user was not authenticated in getChangesForAccount');

            $jsonObject = (object)array(
                "status" => "must be authenticated"
            );

            return new JsonResponse($jsonObject);
        }

        $account = $authContext->getAccount();

        /** @var ForumStatsService $statsService */
        $statsService = $this->get(ForumStatsService::SERVICE_NAME);

        $unreadPosts = $statsService->getUnreadPostsForAccount($account);

        $postMapper = new PostMapper();

        $jsonObject = (object)array(
            "accountId"   => $account->getId(),
            "displayName" => $account->getDisplayName(),
            "unreadPosts" => $postMapper->mapPostsAndTopic($this, $unreadPosts),
            "links"       => (object)array(
                "self"  => $this->generateUrl('getUnreadForAccount', array(), true)
            )
        );

        return new JsonResponse($jsonObject);
    }
}
