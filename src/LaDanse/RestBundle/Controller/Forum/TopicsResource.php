<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\RestBundle\Controller\Forum;

use LaDanse\RestBundle\Common\AbstractRestController;
use LaDanse\RestBundle\Controller\Forum\ResourceHelper;
use LaDanse\RestBundle\Controller\Forum\TopicMapper;

use LaDanse\ServicesBundle\Service\Forum\ForumService;
use LaDanse\ServicesBundle\Service\Forum\TopicDoesNotExistException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/topics")
 */
class TopicsResource extends AbstractRestController
{
    /**
     * @param Request $request
     * @param string $topicId
     *
     * @return Response
     *
     * @Route("/{topicId}", name="getPostsInTopic")
     * @Method({"GET"})
     */
    public function getTopicAction(Request $request, $topicId)
    {
        /** @var ForumService $forumService */
        $forumService = $this->get(ForumService::SERVICE_NAME);

        try
        {
            $topic = $forumService->getTopic($topicId);
        }
        catch (TopicDoesNotExistException $e)
        {
            return ResourceHelper::createErrorResponse(
                $request,
                Response::HTTP_NOT_FOUND,
                $e->getMessage(),
                array("Allow" => "GET")
            );
        }

        $topicMapper = new TopicMapper();

        $jsonObject = $topicMapper->mapTopicAndPosts($this, $topic);

        return new JsonResponse($jsonObject);
    }

    /**
     * @param Request $request
     * @param string $topicId
     *
     * @return Response
     *
     * @Route("/{topicId}/posts", name="createPostInTopic")
     * @Method({"POST", "PUT"})
     */
    public function createPostInTopicAction(Request $request, $topicId)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        if (!$authContext->isAuthenticated())
        {
            $this->getLogger()->warning(__CLASS__ . ' the user was not authenticated in createPost');

            $jsonObject = (object)array(
                "status" => "must be authenticated"
            );

            return new JsonResponse($jsonObject);
        }

        $jsonData = $request->getContent(false);

        $jsonObject = json_decode($jsonData);

        /** @var ForumService $forumService */
        $forumService = $this->get(ForumService::SERVICE_NAME);

        try
        {
            $forumService->createPost(
                $authContext->getAccount(),
                $topicId,
                $jsonObject->message);
        }
        catch (TopicDoesNotExistException $e)
        {
            return ResourceHelper::createErrorResponse(
                $request,
                Response::HTTP_NOT_FOUND,
                $e->getMessage(),
                array("Allow" => "GET")
            );
        }

        $jsonObject = (object)array(
            "status" => "post created in topic"
        );

        return new JsonResponse($jsonObject);
    }

    /**
     * @param Request $request
     * @param string $topicId
     *
     * @return Response
     *
     * @Route("/{topicId}", name="updateTopic")
     * @Method({"POST", "PUT"})
     */
    public function updateTopicAction(Request $request, $topicId)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        /** @var ForumService $forumService */
        $forumService = $this->get(ForumService::SERVICE_NAME);

        $topic = null;

        try
        {
            $topic = $forumService->getTopic($topicId);
        }
        catch (TopicDoesNotExistException $e)
        {
            return ResourceHelper::createErrorResponse(
                $request,
                Response::HTTP_NOT_FOUND,
                $e->getMessage(),
                array("Allow" => "GET")
            );
        }

        if (!($topic->getCreator()->getId() == $authContext->getAccount()->getId()))
        {
            return ResourceHelper::createErrorResponse(
                $request,
                Response::HTTP_FORBIDDEN,
                'Not allowed',
                array("Allow" => "GET")
            );
        }

        $jsonData = $request->getContent(false);

        $jsonObject = json_decode($jsonData);

        $forumService->updateTopic(
            $authContext->getAccount(),
            $topicId,
            $jsonObject->subject
        );

        $jsonObject = (object)array(
            "posts" => "test"
        );

        return new JsonResponse($jsonObject);
    }
}
