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

use LaDanse\ForumBundle\Service\PostDoesNotExistException;

/**
 * @Route("/posts")
 */
class PostsResource extends LaDanseController
{
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
        try
        {
            $post = $this->getForumService()->getPost($postId);
        }
        catch (PostDoesNotExistException $e)
        {
            return ResourceHelper::createErrorResponse(
                $request,
                Response::HTTP_NOT_FOUND,
                $e->getMessage(),
                array("Allow" => "GET")
            );
        }

        $postMapper = new PostMapper();

        $jsonObject = $postMapper->mapPost($this, $post);

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
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        $post = null;

        try
        {
            $post = $this->getForumService()->getPost($postId);
        }
        catch (PostDoesNotExistException $e)
        {
            return ResourceHelper::createErrorResponse(
                $request,
                Response::HTTP_NOT_FOUND,
                $e->getMessage(),
                array("Allow" => "GET")
            );
        }

        if (!($post->getPoster()->getId() == $authContext->getAccount()->getId()))
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

        $this->getForumService()->updatePost(
            $authContext->getAccount(),
            $postId,
            $jsonObject->message);

        $jsonObject = (object)array(
            "posts" => "test"
        );

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
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        if (!$authContext->isAuthenticated())
        {
            $this->getLogger()->warning(__CLASS__ . ' the user was not authenticated in markPostAsRead');

            $jsonObject = (object)array(
                "status" => "must be authenticated"
            );

            return new JsonResponse($jsonObject);
        }

        $account = $authContext->getAccount();
        $statsService = $this->getForumStatsService();

        $statsService->markPostAsRead($account, $postId);

        $jsonObject = (object)array(
            "status" => "200"
        );

        return new JsonResponse($jsonObject);
    }
}
