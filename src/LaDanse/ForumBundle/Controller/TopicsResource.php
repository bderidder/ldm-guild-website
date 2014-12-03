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

use LaDanse\ForumBundle\Service\ForumDoesNotExistException;
use LaDanse\ForumBundle\Service\TopicDoesNotExistException;

/**
 * @Route("/forums/{forumId}/topics")
 */
class TopicsResource extends LaDanseController
{
    /**
     * @param Request $request
     * @param string $forumId
     *
     * @return Response
     *
     * @Route("/", name="getTopics")
     * @Method({"GET"})
     */
    public function getTopicsAction(Request $request, $forumId)
    {
        try
        {
            $topics = $this->getForumService()->getAllTopicsInForum($forumId);
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

        usort(
            $topics,
            function ($a, $b) {
                /** @var $a \LaDanse\ForumBundle\Entity\Topic */
                /** @var $b \LaDanse\ForumBundle\Entity\Topic */

                return $a->getCreateDate() > $b->getCreateDate();
            }
        );

        $jsonArray = array();

        foreach ($topics as $topic)
        {
            /** @var $topic Topic */

            $jsonArray[] = $this->topicToJson($topic);
        }

        $jsonObject = (object)array(
            "topics" => $jsonArray,
            "links" => (object)array(
                "self" => $this->generateUrl('getTopics', array('forumId' => $forumId), true)
            )
        );

        return new JsonResponse($jsonObject);
    }

    /**
     * @param Request $request
     * @param string $topicId
     *
     * @return Response
     *
     * @Route("/", name="createTopic")
     * @Method({"POST", "PUT"})
     */
    public function createTopicAction(Request $request, $topicId)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        $jsonData = $request->getContent(false);

        $logger = $this->get('logger');
        $logger->info('JSON DATA ' . $jsonData);

        $jsonObject = json_decode($jsonData);

        $this->getForumService()->createPost($topicId, $authContext->getAccount(), $jsonObject->message);

        $jsonObject = (object)array(
            "posts" => "test"
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

        $this->getForumService()->updateTopic($topicId, $jsonObject->subject);

        $jsonObject = (object)array(
            "posts" => "test"
        );

        return new JsonResponse($jsonObject);
    }

    /**
     * @param Request $request
     * @param string $topicId
     * @param string $commentId
     *
     * @return Response
     *
     * @Route("/{commentId}", name="otherPost")
     * @Method({"POST", "PUT", "DELETE", "OPTIONS"})
     */
    public function otherPostAction(Request $request, $topicId, $commentId)
    {
        $this->getLogger()->warning(
            'POST/PUT/DELETE/OPTIONS for Comment resource with ' . $topicId . ' and ' . $commentId
        );

        return ResourceHelper::createErrorResponse(
            $request,
            Response::HTTP_NOT_FOUND,
            "Resource not found",
            array("Allow" => "GET")
        );
    }

    /**
     * @param Topic $topic
     *
     * @return object
     */
    private function topicToJson(Topic $topic)
    {
        return (object)array(
            "topicId" => $topic->getId(),
            "creatorId" => $topic->getCreator()->getId(),
            "creator" => $topic->getCreator()->getDisplayName(),
            "subject" => $topic->getSubject(),
            "createDate" => $topic->getCreateDate()->format(\DateTime::ISO8601),
            "links" => (object)array(
                "self" => $this->generateUrl('getPosts', array('topicId' => $topic->getId()), true)
            )
        );
    }
}
