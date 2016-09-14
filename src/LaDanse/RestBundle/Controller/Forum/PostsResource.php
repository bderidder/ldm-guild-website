<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\RestBundle\Controller\Forum;

use LaDanse\RestBundle\Common\AbstractRestController;
use LaDanse\ServicesBundle\Service\Forum\ForumService;
use LaDanse\ServicesBundle\Service\Forum\ForumStatsService;
use LaDanse\ServicesBundle\Service\Forum\PostDoesNotExistException;
use LaDanse\SiteBundle\Security\AuthenticationService;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * @Route("/posts")
 */
class PostsResource extends AbstractRestController
{
    /**
     * @DI\Inject("monolog.logger.ladanse")
     * @var LoggerInterface $logger
     */
    private $logger;

    /**
     * @param Request $request
     * @param string $postId
     *
     * @return Response
     *
     * @Route("/{postId}", name="getPost")
     * @Method({"GET"})
     */
    public function getPostAction(Request $request, $postId)
    {
        /** @var ForumService $forumService */
        $forumService = $this->get(ForumService::SERVICE_NAME);

        try
        {
            $post = $forumService->getPost($postId);
        }
        catch (PostDoesNotExistException $e)
        {
            return ResourceHelper::createErrorResponse(
                $request,
                Response::HTTP_NOT_FOUND,
                $e->getMessage(),
                ["Allow" => "GET"]
            );
        }

        $postMapper = new PostMapper();

        $jsonObject = $postMapper->mapPost($this->get('router'), $post);

        return new JsonResponse($jsonObject);
    }

    /**
     * @param Request $request
     * @param string $postId
     *
     * @return Response
     *
     * @Route("/{postId}", name="updatePost")
     * @Method({"POST", "PUT"})
     */
    public function updatePostAction(Request $request, $postId)
    {
        /** @var AuthenticationService $authenticationService */
        $authenticationService = $this->get(AuthenticationService::SERVICE_NAME);
        $authContext = $authenticationService->getCurrentContext();

        /** @var ForumService $forumService */
        $forumService = $this->get(ForumService::SERVICE_NAME);

        $post = null;

        try
        {
            $post = $forumService->getPost($postId);
        }
        catch (PostDoesNotExistException $e)
        {
            return ResourceHelper::createErrorResponse(
                $request,
                Response::HTTP_NOT_FOUND,
                $e->getMessage(),
                ["Allow" => "GET"]
            );
        }

        if (!($post->getPoster()->getId() == $authContext->getAccount()->getId()))
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

        $forumService->updatePost(
            $authContext->getAccount(),
            $postId,
            $jsonObject->message);

        $jsonObject = (object)[
            "posts" => "test"
        ];

        return new JsonResponse($jsonObject);
    }

    /**
     * @param string $postId
     *
     * @return Response
     *
     * @Route("/{postId}/markRead", name="markPostAsRead")
     * @Method({"GET", "POST", "PUT"})
     */
    public function markPostAsReadAction($postId)
    {
        /** @var AuthenticationService $authenticationService */
        $authenticationService = $this->get(AuthenticationService::SERVICE_NAME);
        $authContext = $authenticationService->getCurrentContext();

        if (!$authContext->isAuthenticated())
        {
            $this->logger->warning(__CLASS__ . ' the user was not authenticated in markPostAsRead');

            $jsonObject = (object)[
                "status" => "must be authenticated"
            ];

            return new JsonResponse($jsonObject);
        }

        $account = $authContext->getAccount();

        /** @var ForumStatsService $statsService */
        $statsService = $this->get(ForumStatsService::SERVICE_NAME);

        $statsService->markPostAsRead($account, $postId);

        $jsonObject = (object)[
            "status" => "200"
        ];

        return new JsonResponse($jsonObject);
    }
}
