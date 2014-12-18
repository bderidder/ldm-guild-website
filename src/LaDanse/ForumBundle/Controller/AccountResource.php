<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ForumBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use LaDanse\CommonBundle\Helper\LaDanseController;

use LaDanse\ForumBundle\Entity\Topic;

use LaDanse\ForumBundle\Service\TopicDoesNotExistException;

/**
 * @Route("/account")
 */
class AccountResource extends LaDanseController
{
    /**
     * @return Response
     *
     * @Route("/changesForAccount", name="getChangesForAccount")
     * @Method({"GET"})
     */
    public function getChangesForAccount()
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
        $statsService = $this->getForumStatsService();

        $lastVisitDate = $statsService->getLastVisitForAccount($account, $this->defaultLastVisitDate());

        $recentPosts = $statsService->getNewPostsSince($lastVisitDate);
        $recentTopics = $statsService->getNewTopicsSince($lastVisitDate);

        $postMapper = new PostMapper();
        $topicMapper = new TopicMapper();

        $jsonObject = (object)array(
            "accountId"   => $account->getId(),
            "displayName" => $account->getDisplayName(),
            "newPosts"    => $postMapper->mapPostsAndTopic($this, $recentPosts),
            "newTopics"   => $topicMapper->mapTopicsAndForum($this, $recentTopics),
            "links"       => (object)array(
                "self"  => $this->generateUrl('getChangesForAccount', array(), true),
                "reset" => $this->generateUrl('resetChangesForAccount', array(), true)
            )
        );

        return new JsonResponse($jsonObject);
    }

    /**
     * @return Response
     *
     * @Route("/changesForAccount/reset", name="resetChangesForAccount")
     * @Method({"GET"})
     */
    public function resetChangesForAccount()
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
        $statsService = $this->getForumStatsService();

        $statsService->resetLastVisitForAccount($account);

        $jsonObject = (object)array(
            "status" => 200
        );

        return new JsonResponse($jsonObject);
    }

    /**
     * @return \DateTime
     */
    private function defaultLastVisitDate()
    {
        return new \DateTime();
    }
}
