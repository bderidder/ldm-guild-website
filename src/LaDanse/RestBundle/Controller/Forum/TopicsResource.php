<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\RestBundle\Controller\Forum;

use LaDanse\RestBundle\Common\AbstractRestController;
use LaDanse\ServicesBundle\Service\Forum\ForumService;
use LaDanse\ServicesBundle\Service\Forum\TopicDoesNotExistException;
use LaDanse\SiteBundle\Security\AuthenticationService;
use Psr\Log\LoggerInterface;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * @Route("/topics")
 */
class TopicsResource extends AbstractRestController
{
    /**
     * @DI\Inject("monolog.logger.ladanse")
     * @var LoggerInterface $logger
     */
    private $logger;

    /**
     * @param Request $request
     * @param string $topicId
     *
     * @return Response
     *
     * @Route("/{topicId}", name="getPostsInTopic", methods={"GET"})
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
                ["Allow" => "GET"]
            );
        }

        $topicMapper = new TopicMapper();

        $jsonObject = $topicMapper->mapTopicAndPosts($this->get('router'), $topic);

        return new JsonResponse($jsonObject);
    }

    /**
     * @param Request $request
     * @param string $topicId
     *
     * @return Response
     *
     * @Route("/{topicId}/posts", name="createPostInTopic", methods={"POST", "PUT"})
     */
    public function createPostInTopicAction(Request $request, $topicId)
    {
        /** @var AuthenticationService $authenticationService */
        $authenticationService = $this->get(AuthenticationService::SERVICE_NAME);
        $authContext = $authenticationService->getCurrentContext();

        if (!$authContext->isAuthenticated())
        {
            $this->logger->warning(__CLASS__ . ' the user was not authenticated in createPost');

            $jsonObject = (object)[
                "status" => "must be authenticated"
            ];

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
                ["Allow" => "GET"]
            );
        }

        $jsonObject = (object)[
            "status" => "post created in topic"
        ];

        return new JsonResponse($jsonObject);
    }

    /**
     * @param Request $request
     * @param string $topicId
     *
     * @return Response
     *
     * @Route("/{topicId}", name="updateTopic", methods={"POST", "PUT"})
     */
    public function updateTopicAction(Request $request, $topicId)
    {
        /** @var AuthenticationService $authenticationService */
        $authenticationService = $this->get(AuthenticationService::SERVICE_NAME);
        $authContext = $authenticationService->getCurrentContext();

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
                ["Allow" => "GET"]
            );
        }

        if (!($topic->getCreator()->getId() == $authContext->getAccount()->getId()))
        {
            return ResourceHelper::createErrorResponse(
                $request,
                Response::HTTP_FORBIDDEN,
                'Not allowed',
                ["Allow" => "GET"]
            );
        }

        $jsonData = $request->getContent(false);

        $jsonObject = json_decode($jsonData);

        $forumService->updateTopic(
            $authContext->getAccount(),
            $topicId,
            $jsonObject->subject
        );

        $jsonObject = (object)[
            "posts" => "test"
        ];

        return new JsonResponse($jsonObject);
    }
}
