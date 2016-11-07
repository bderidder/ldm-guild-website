<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\RestBundle\Controller\Forum;

use LaDanse\RestBundle\Common\AbstractRestController;
use LaDanse\ServicesBundle\Service\Forum\ForumDoesNotExistException;
use LaDanse\ServicesBundle\Service\Forum\ForumService;
use LaDanse\SiteBundle\Security\AuthenticationService;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\DiExtraBundle\Annotation as DI;

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
     * @DI\Inject("monolog.logger.ladanse")
     * @var LoggerInterface $logger
     */
    private $logger;

    /**
     * @return Response
     *
     * @Route("/", name="getForumList")
     * @Method({"GET"})
     */
    public function getForumListAction()
    {
        /** @var ForumService $forumService */
        $forumService = $this->get(ForumService::SERVICE_NAME);

        $forums = $forumService->getAllForums();

        $forumMapper = new ForumMapper();

        $jsonObject = $forumMapper->mapForums($this->get('router'), $forums);

        return new JsonResponse($jsonObject);
    }

    /**
     * @return Response
     *
     * @Route("/activity", name="getActivityForForums")
     * @Method({"GET"})
     */
    public function getActivityForForumsAction()
    {
        /** @var ForumService $forumService */
        $forumService = $this->get(ForumService::SERVICE_NAME);

        $posts = $forumService->getActivityForForums();

        $postMapper = new PostMapper();

        $jsonObject = (object)[
            "posts"   => $postMapper->mapPostsAndTopic($this->get('router'), $posts),
            "links"   => (object)[
                "self"  => $this->generateUrl('getActivityForForums', [], true)
            ]
        ];

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
                ["Allow" => "GET"]
            );
        }

        $forumMapper = new ForumMapper();

        $jsonObject = $forumMapper->mapForumAndTopics($this->get('router'), $forum);

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
    public function getActivityForForumAction(Request $request, $forumId)
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
                ["Allow" => "GET"]
            );
        }

        $posts = $forumService->getActivityForForum($forumId);

        $postMapper = new PostMapper();

        $jsonObject = (object)[
            "posts"   => $postMapper->mapPostsAndTopic($this->get('router'), $posts),
            "links"   => (object)[
                "self"  => $this->generateUrl('getActivityForForum', ['forumId' => $forumId], true)
            ]
        ];

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
        /** @var AuthenticationService $authenticationService */
        $authenticationService = $this->get(AuthenticationService::SERVICE_NAME);
        $authContext = $authenticationService->getCurrentContext();

        if (!$authContext->isAuthenticated())
        {
            $this->logger->warning(__CLASS__ . ' the user was not authenticated in calendarIndex');

            $jsonObject = (object)[
                "status" => "must be authenticated"
            ];

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
                ["Allow" => "GET"]
            );
        }

        $jsonObject = (object)[
            "status" => "topic created in forum"
        ];

        return new JsonResponse($jsonObject);
    }
}
