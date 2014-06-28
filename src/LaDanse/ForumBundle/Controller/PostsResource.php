<?php

namespace LaDanse\ForumBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\JsonResponse;

use LaDanse\CommonBundle\Helper\LaDanseController;

use LaDanse\SiteBundle\Security\AuthenticationContext;

use Latte\Bundle\GuestbookRestBundle\Entity\GuestComment;

use LaDanse\ForumBundle\Entity\Forum,
    LaDanse\ForumBundle\Entity\Topic,
    LaDanse\ForumBundle\Entity\Post,
    LaDanse\DomainBundle\Entity\Account;

use LaDanse\ForumBundle\Service\TopicDoesNotExistException;

/** 
 * @Route("/topics/{topicId}/posts")
*/
class PostsResource extends LaDanseController
{
    /**
     * @Route("/", name="getPosts")
     * @Method({"GET"})
     */
    public function getPostsAction(Request $request, $topicId)
    {
        try
        {
            $posts = $this->getForumService()->getAllPosts($topicId);
        }
        catch(TopicDoesNotExistException $e)
        {
            return ResourceHelper::createErrorResponse($request, 
                    Response::HTTP_NOT_FOUND, $e->getMessage(), array("Allow" => "GET"));
        }

        usort($posts, function($a, $b)
            {
                return $a->getPostDate() > $b->getPostDate();
            }
        );

        $jsonArray = array();

        foreach($posts as $post)
        {
            $jsonArray[] = $this->postToJson($post);
        }

        $jsonObject = (object)array(
            "posts"      => $jsonArray,
            "links"      => (object)array(
                "self"   => $this->generateUrl('getPosts', array('topicId' => $topicId), true),
                "topic"  => $this->generateUrl('getTopic', array('topicId' => $topicId), true)
            )
        );

        return new JsonResponse($jsonObject);
    }

    /**
     * @Route("/create", name="createPost")
     * @Method({"GET"})
     */
    public function createTopicAction(Request $request, $topicId)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        $this->getForumService()->createPost($topicId, $authContext->getAccount(), 'This is a message');
    }

    /**
     * @Route("/{postId}", name="getPost")
     * @Method({"GET"})
     */
    public function getTopicAction(Request $request, $topicId)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        return ResourceHelper::createErrorResponse($request, 
            Response::HTTP_NOT_FOUND, "Resource not found", array("Allow" => "GET"));
    }

    /**
     * @Route("/", name="createPost")
     * @Method({"POST", "PUT"})
     */
    public function createPostAction(Request $request, $topicId)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        $jsonData = $request->getContent(false);

        $logger = $this->get('logger');
        $logger->info('JSON DATA ' . $jsonData);

        $jsonObject = json_decode($jsonData);

        $this->getForumService()->createPost($topicId, $authContext->getAccount(), $jsonObject->message);

        $jsonObject = (object)array(
            "posts"    => "test"
        );

        return new JsonResponse($jsonObject);
    }

    /**
     * @Route("/{postId}", name="updatePost")
     * @Method({"POST", "PUT"})
     */
    public function updatePostAction(Request $request, $topicId, $postId)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        $post = null;

        try
        {
            $post = $this->getForumService()->getPost($postId);
        }
        catch(TopicDoesNotExistException $e)
        {
            return ResourceHelper::createErrorResponse($request, 
                    Response::HTTP_NOT_FOUND, $e->getMessage(), array("Allow" => "GET"));
        }

        if (!($post->getPoster()->getId() == $authContext->getAccount()->getId()))
        {
            return ResourceHelper::createErrorResponse($request, 
                    Response::HTTP_FORBIDDEN, 'Not allowed', array("Allow" => "GET"));
        }

        $jsonData = $request->getContent(false);

        $logger = $this->get('logger');
        $logger->info('JSON DATA ' . $jsonData);

        $jsonObject = json_decode($jsonData);

        $this->getForumService()->updatePost($postId, $jsonObject->message);

        $jsonObject = (object)array(
            "posts"    => "test"
        );

        return new JsonResponse($jsonObject);
    }

    /**
     * @Route("/comments", name="putPost")
     * @Method({"POST"})
     */
    public function createTempPostAction(Request $request, $topicId)
    {
        $logger = $this->get('logger');
        $logger->info('Post request received');

        $guestbook = $this->fetchGuestbook($guestbookId);

        if ($guestbook === NULL)
        {
            return ResourceHelper::createErrorResponse($request, Response::HTTP_NOT_FOUND, "Resource not found");
        }

        // $_POST parameters
        $subject = $request->request->get('subject');
        $poster = $request->request->get('poster');
        $message = $request->request->get('message');

        if ((isset($subject) && (strlen($subject) > 2)) &&
            (isset($poster) && (strlen($poster) > 2)) &&
            (isset($message) && (strlen($message))))
        {
            $logger->info('Subject is ' . $subject);
            $logger->info('Poster is ' . $poster);
            $logger->info('Message is ' . $message);

            $newComment = new GuestComment();
            $newComment->setId(ResourceHelper::createUUID());
            $newComment->setPoster($poster);
            $newComment->setSubject($subject);
            $newComment->setMessage($message);
            $newComment->setGuestbook($guestbook);
            $newComment->setPostDate(new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($newComment);
            $em->flush();

            $response = new JsonResponse();

            ResourceHelper::addAccessControlAllowOrigin($request, $response);

            return $response;
        }
        else
        {
            return ResourceHelper::createErrorResponse($request, Response::HTTP_BAD_REQUEST, "Not all fields are provided");
        }
    }

    /**
     * @Route("/{commentId}", name="otherPost")
     * @Method({"POST", "PUT", "DELETE", "OPTIONS"})
     */
    public function otherPostAction(Request $request, $topicId, $postId)
    {
        return ResourceHelper::createErrorResponse($request, 
            Response::HTTP_NOT_FOUND, "Resource not found", array("Allow" => "GET"));
    }

    /**
     * @param Post $post
     * @return object
     */
    private function postToJson(Post $post)
    {
        return (object)array(
            "postId"    => $post->getId(),
            "posterId"    => $post->getPoster()->getId(),
            "poster"    => $post->getPoster()->getUsername(),
            "message"   => $post->getMessage(),
            "postDate"  => $post->getPostDate()->format(\DateTime::ISO8601),
            "links"     => (object)array(
                "self"  => $this->generateUrl('getPost', 
                    array('postId' => $post->getId(),
                          'topicId' => $post->getTopic()->getId()), true),
            )
        );
    }
}
