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

use LaDanse\ForumBundle\Service\ForumDoesNotExistException;

/**
 * Class ForumsResource
 *
 * @package LaDanse\ForumBundle\Controller
 *
 * @Route("/forums")
 */
class ForumsResource extends LaDanseController
{
    /**
     * @return Response
     *
     * @Route("/", name="getForumList")
     * @Method({"GET"})
     */
    public function getForumList()
    {
        $forums = $this->getForumService()->getAllForums();

        $forumMapper = new ForumMapper();

        $jsonObject = $forumMapper->mapForums($this, $forums);

        return new JsonResponse($jsonObject);
    }

    /**
     * @param Request $request
     * @param string $forumId
     *
     * @return Response
     *
     * @Route("/{forumId}", name="getForum")
     * @Method({"GET"})
     */
    public function getForumForIdAction(Request $request, $forumId)
    {
        try
        {
            $forum = $this->getForumService()->getForum($forumId);
        }
        catch (ForumDoesNotExistException $e)
        {
            return ResourceHelper::createErrorResponse(
                $request,
                Response::HTTP_NOT_FOUND,
                $e->getMessage(),
                array("Allow" => "GET")
            );
        }

        $forumMapper = new ForumMapper();

        $jsonObject = $forumMapper->mapForumAndTopics($this, $forum);

        return new JsonResponse($jsonObject);
    }

    /**
     * @param Request $request
     * @param string $forumId
     *
     * @return Response
     *
     * @Route("/{forumId}/activity", name="getActivityForForum")
     * @Method({"GET"})
     */
    public function getActivityForForum(Request $request, $forumId)
    {
        try
        {
            $forum = $this->getForumService()->getForum($forumId);
        }
        catch (ForumDoesNotExistException $e)
        {
            return ResourceHelper::createErrorResponse(
                $request,
                Response::HTTP_NOT_FOUND,
                $e->getMessage(),
                array("Allow" => "GET")
            );
        }

        $posts = $this->getForumService()->getActivityForForum($forumId);

        $postMapper = new PostMapper();

        $jsonObject = (object)array(
            "posts"   => $postMapper->mapPostsAndTopic($this, $posts),
            "links"   => (object)array(
                "self"  => $this->generateUrl('getActivityForForum', array('forumId' => $forumId), true)
            )
        );

        return new JsonResponse($jsonObject);
    }

    /**
     * @param Request $request
     * @param string $forumId
     *
     * @return Response
     *
     * @Route("/{forumId}/topics", name="createTopic")
     * @Method({"POST", "PUT"})
     */
    public function createTopicAction(Request $request, $forumId)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        if (!$authContext->isAuthenticated())
        {
            $this->getLogger()->warning(__CLASS__ . ' the user was not authenticated in calendarIndex');

            $jsonObject = (object)array(
                "status" => "must be authenticated"
            );

            return new JsonResponse($jsonObject);
        }

        $jsonData = $request->getContent(false);

        $jsonObject = json_decode($jsonData);

        try
        {
            $this->getForumService()->createTopicInForum(
                $authContext->getAccount(),
                $forumId,
                $jsonObject->subject,
                $jsonObject->text
            );
        }
        catch (ForumDoesNotExistException $e)
        {
            return ResourceHelper::createErrorResponse(
                $request,
                Response::HTTP_NOT_FOUND,
                $e->getMessage(),
                array("Allow" => "GET")
            );
        }

        $jsonObject = (object)array(
            "status" => "topic created in forum"
        );

        return new JsonResponse($jsonObject);
    }
}
