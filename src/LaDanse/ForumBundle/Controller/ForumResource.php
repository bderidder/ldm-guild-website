<?php

namespace LaDanse\ForumBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\JsonResponse;

use Latte\Bundle\GuestbookRestBundle\Entity\GuestComment;

/**
 * @Route("/forums")
*/
class ForumResource extends Controller
{
    /**
     * @Route("/", name="allGuestbooks")
     * @Method({"GET", "POST", "DELETE", "PUT", "HEAD"})
     */
    public function allGuestbooksAction(Request $request)
    {
        return ResourceHelper::createErrorResponse($request, Response::HTTP_NOT_FOUND, "Resource not found", array("Allow" => "GET"));
    }

    /**
     * @Route("/{guestbookId}", name="getGuestbook")
     * @Method({"GET"})
     */
    public function getGuestbookAction(Request $request, $guestbookId)
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
     * @Route("/{guestbookId}", name="otherGuestbook")
     * @Method({"GET", "POST", "PUT", "HEAD"})
     */
    public function otherGuestbookAction(Request $request, $guestbookId)
    {
        return ResourceHelper::createErrorResponse($request, Response::HTTP_METHOD_NOT_ALLOWED, "Method not allowed", array("Allow" => "GET"));
    }

    /**
     * @Route("/{guestbookId}/comments", name="getGuestbookComments")
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
     * @Route("/{guestbookId}/comments", name="optionsGuestbookComments")
     * @Method({"OPTIONS"})
     */
    public function optionsCommentsAction(Request $request, $guestbookId)
    {
        $response = new JsonResponse();

        ResourceHelper::addAccessControlAllowOrigin($request, $response);

        return $response;
    }

    /**
     * @Route("/{guestbookId}/comments", name="postGuestbookComments")
     * @Method({"POST"})
     */
    public function postCommentsAction(Request $request, $guestbookId)
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
