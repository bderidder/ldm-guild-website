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
     * @Route("/{forumId}", name="createTopic")
     * @Method({"POST", "PUT"})
     */
    public function createTopicAction(Request $request, $forumId)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        $jsonData = $request->getContent(false);

        $jsonObject = json_decode($jsonData);

        try
        {
            $this->getForumService()->createTopicInForum(
                $authContext->getAccount(),
                $forumId,
                $jsonObject->subject
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
