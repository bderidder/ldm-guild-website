<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\RestBundle\Controller\Forum;

use LaDanse\RestBundle\Common\AbstractRestController;
use LaDanse\RestBundle\Controller\Forum\ForumMapper;
use LaDanse\RestBundle\Controller\Forum\PostMapper;
use LaDanse\RestBundle\Controller\Forum\ResourceHelper;

use LaDanse\ServicesBundle\Service\Forum\ForumDoesNotExistException;
use LaDanse\ServicesBundle\Service\Forum\ForumService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class ForumsResource
 *
 * @package LaDanse\ForumBundle\Controller
 *
 * @Route("/forums")
 */
class ForumsResource extends AbstractRestController
{
    /**
     * @return Response
     *
     * @Route("/", name="getForumList")
     * @Method({"GET"})
     */
    public function getForumList()
    {
        /** @var ForumService $forumService */
        $forumService = $this->get(ForumService::SERVICE_NAME);

        $forums = $forumService->getAllForums();

        $forumMapper = new ForumMapper();

        $jsonObject = $forumMapper->mapForums($this, $forums);

        return new JsonResponse($jsonObject);
    }

    /**
     * @return Response
     *
     * @Route("/activity", name="getActivityForForums")
     * @Method({"GET"})
     */
    public function getActivityForForums()
    {
        /** @var ForumService $forumService */
        $forumService = $this->get(ForumService::SERVICE_NAME);

        $posts = $forumService->getActivityForForums();

        $postMapper = new PostMapper();

        $jsonObject = (object)array(
            "posts"   => $postMapper->mapPostsAndTopic($this, $posts),
            "links"   => (object)array(
                "self"  => $this->generateUrl('getActivityForForums', array(), true)
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
     * @Route("/{forumId}", name="getForum")
     * @Method({"GET"})
     */
    public function getForumForIdAction(Request $request, $forumId)
    {
        /** @var ForumService $forumService */
        $forumService = $this->get(ForumService::SERVICE_NAME);

        try
        {
            $forum = $forumService->getForum($forumId);
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
        /** @var ForumService $forumService */
        $forumService = $this->get(ForumService::SERVICE_NAME);

        try
        {
            $forumService->getForum($forumId);
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

        $posts = $forumService->getActivityForForum($forumId);

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
            /** @var ForumService $forumService */
            $forumService = $this->get(ForumService::SERVICE_NAME);

            $forumService->createTopicInForum(
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
