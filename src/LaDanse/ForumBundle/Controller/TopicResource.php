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

/**
 * @Route("/topics")
*/
class TopicResource extends LaDanseController
{
    /**
     * @Route("/", name="allTopics")
     * @Method({"GET", "POST", "DELETE", "PUT", "HEAD"})
     */
    public function allTopicsAction(Request $request)
    {
        return ResourceHelper::createErrorResponse($request, 
            Response::HTTP_NOT_FOUND, "Resource not found", array("Allow" => "GET"));
    }

    /**
     * @Route("/create", name="createTopic")
     * @Method({"GET"})
     */
    public function createTopicAction(Request $request)
    {
        $authContext = $this->getAuthenticationService()->getCurrentContext();

        $doc = $this->getDoctrine();

        $em = $doc->getManager();

        $topic = new Topic();

        $topic->setId(ResourceHelper::createUUID());
        $topic->setCreateDate(new \DateTime());
        $topic->setCreator($authContext->getAccount());
        $topic->setSubject("This is a test topic");

        $em->persist($topic);
        $em->flush();
    }

    /**
     * @Route("/", name="otherTopics")
     * @Method({"POST", "DELETE", "PUT", "HEAD"})
     */
    public function otherTopicsAction(Request $request)
    {
        return ResourceHelper::createErrorResponse($request, 
            Response::HTTP_NOT_FOUND, "Resource not found", array("Allow" => "GET"));
    }

    /**
     * @Route("/{topicId}", name="getTopic")
     * @Method({"GET"})
     */
    public function getTopicAction(Request $request, $topicId)
    {
        $guestbook = $this->fetchGuestbook($guestbookId);

        if (null === $guestbook)
        {
            return ResourceHelper::createErrorResponse($request, Response::HTTP_NOT_FOUND, "Resource not found");
        }
        else
        {
            $jsonObject = (object)array(
                "guestbookId" => $guestbookId,
                "domainId"    => $guestbook->getDomainId(),
                "numComments" => count($guestbook->getComments()),
                "links"       => (object)array(           
                    "self"     => $this->generateUrl('getGuestbook', array('guestbookId' => $guestbookId), true),
                    "comments" => $this->generateUrl('getGuestbookComments', array('guestbookId' => $guestbookId), true)   
                )
            );

            return new JsonResponse($jsonObject); 
        }    	
    }

    /**
     * @Route("/{topicId}", name="otherTopic")
     * @Method({"GET", "POST", "PUT", "HEAD"})
     */
    public function otherTopicAction(Request $request, $guestbookId)
    {
        return ResourceHelper::createErrorResponse($request, 
            Response::HTTP_METHOD_NOT_ALLOWED, "Method not allowed", array("Allow" => "GET"));
    }

    /**
     * @Route("/{topicId}/comments", name="getGuestbookComments")
     * @Method({"GET"})
     */
    public function getCommentsAction(Request $request, $guestbookId)
    {
        $guestbook = $this->fetchGuestbook($guestbookId);

        $attrComments = $guestbook->getComments()->toArray();

        usort($attrComments, array('Latte\Bundle\GuestbookRestBundle\Json\DateTimeComparator', 'compareRecentFirst'));

        $jsonArray = array();

        foreach($attrComments as $guestComment)
        {
            //$jsonArray[] = $this->generateUrl('getComment', array('commentId' => $guestComment->getId()), true);
            $jsonArray[] = $this->commentToJson($guestComment);
        }

        $jsonObject = (object)array(
            "comments" => $jsonArray,
            "links"    => (object)array(
                "guestbook" => $this->generateUrl('getGuestbook', array('guestbookId' => $guestbook->getId()), true)
            )
        );

        $response = new JsonResponse($jsonObject);

        ResourceHelper::addAccessControlAllowOrigin($request, $response);

        return $response;
    }

    

    /**
     * @Route("/{guestbookId}/comments", name="otherGuestbookComments")
     * @Method({"PUT", "HEAD"})
     */
    public function otherCommentsAction(Request $request, $guestbookId)
    {
        return ResourceHelper::createErrorResponse($request, Response::HTTP_METHOD_NOT_ALLOWED, "Method not allowed", array("Allow" => "GET, POST"));
    }

    /**
     * @param GuestComment $guestComment
     * @return object
     */
    private function commentToJson(GuestComment $guestComment)
    {
        return (object)array(
            "commentId"   => $guestComment->getId(),
            "poster"      => $guestComment->getPoster(),
            "subject"     => $guestComment->getSubject(),
            "message"     => $guestComment->getMessage(),
            "postDate"    => $guestComment->getPostDate()->format(\DateTime::ISO8601),
            "numChildren" => count($guestComment->getComments()),
            "links"         => (object)array(
                "self"      => $this->generateUrl('getComment', array('commentId' => $guestComment->getId()), true),
                "children"  => $this->generateUrl('getCommentChildren', array('commentId' => $guestComment->getId()), true)
            )
        );
    }

    /**
     * @param $guestbookId
     * @return \Latte\Bundle\GuestbookRestBundle\Entity\Guestbook
     */
    private function fetchGuestbook($guestbookId)
    {
        $doc = $this->getDoctrine();
        $repository = $doc->getRepository(self::GUESTBOOK_REPOSITORY);
        
        return $repository->find($guestbookId);   
    }
}
