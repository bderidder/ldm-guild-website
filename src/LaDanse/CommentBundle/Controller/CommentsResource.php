<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\CommentBundle\Controller;

use LaDanse\CommentBundle\Service\CommentDoesNotExistException;
use LaDanse\CommentBundle\Service\CommentGroupDoesNotExistException;

use LaDanse\CommonBundle\Helper\LaDanseController;
use LaDanse\CommonBundle\Helper\ResourceHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/")
 */
class CommentsResource extends LaDanseController
{
    /**
     * @param Request $request
     * @param string $groupId
     *
     * @return Response
     *
     * @Route("/groups/{groupId}", name="getCommentsInGroup")
     * @Method({"GET"})
     */
    public function getCommentsInGroupAction(Request $request, $groupId)
    {
        try
        {
            $group = $this->getCommentService()->getCommentGroup($groupId);
        }
        catch (CommentGroupDoesNotExistException $e)
        {
            return ResourceHelper::createErrorResponse(
                $request,
                Response::HTTP_NOT_FOUND,
                $e->getMessage(),
                array("Allow" => "GET")
            );
        }

        $groupMapper = new CommentGroupMapper();

        $jsonObject = $groupMapper->mapGroupAndComments($this, $group);

        return new JsonResponse($jsonObject);
    }

    /**
     * @param Request $request
     * @param string $groupId
     *
     * @return Response
     *
     * @Route("/groups/{groupId}/comments", name="createComment")
     * @Method({"POST", "PUT"})
     */
    public function createCommentAction(Request $request, $groupId)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        if (!$authContext->isAuthenticated())
        {
            $this->getLogger()->warning(__CLASS__ . ' the user was not authenticated in createComment');

            $jsonObject = (object)array(
                "status" => "must be authenticated"
            );

            return new JsonResponse($jsonObject);
        }

        $jsonData = $request->getContent(false);

        $jsonObject = json_decode($jsonData);

        try
        {
            $this->getCommentService()->createComment($groupId, $authContext->getAccount(), $jsonObject->message);
        }
        catch (CommentGroupDoesNotExistException $e)
        {
            return ResourceHelper::createErrorResponse(
                $request,
                Response::HTTP_NOT_FOUND,
                $e->getMessage(),
                array("Allow" => "GET")
            );
        }

        $jsonObject = (object)array(
            "status" => "comment created in group"
        );

        return new JsonResponse($jsonObject);
    }

    /**
     * @param Request $request
     * @param string $commentId
     *
     * @return Response
     *
     * @Route("/comments/{commentId}", name="updateComment")
     * @Method({"POST", "PUT"})
     */
    public function updateCommentAction(Request $request, $commentId)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        $comment = null;

        try
        {
            $comment = $this->getCommentService()->getComment($commentId);
        }
        catch (CommentDoesNotExistException $e)
        {
            return ResourceHelper::createErrorResponse(
                $request,
                Response::HTTP_NOT_FOUND,
                $e->getMessage(),
                array("Allow" => "GET")
            );
        }

        if (!($comment->getPoster()->getId() == $authContext->getAccount()->getId()))
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

        $this->getCommentService()->updateComment($commentId, $jsonObject->message);

        $jsonObject = (object)array(
            "status" => "OK"
        );

        return new JsonResponse($jsonObject);
    }
}
