<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ForumBundle\Controller;

use LaDanse\CommonBundle\Helper\LaDanseController;
use LaDanse\ForumBundle\Service\TopicDoesNotExistException;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/topics")
 */
class TopicsResource extends LaDanseController
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
        try
        {
            $topic = $this->getForumService()->getTopic($topicId);
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

        try
        {
            $this->getForumService()->createPost(
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

        $topic = null;

        try
        {
            $topic = $this->getForumService()->getTopic($topicId);
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

        $this->getForumService()->updateTopic(
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
