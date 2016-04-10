<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\RestBundle\Controller\Comments;

use LaDanse\DomainBundle\Entity\Comments\Comment;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class CommentMapper
 *
 * @package LaDanse\ForumBundle\Controller
 */
class CommentMapper
{
    /**
     * @param Controller $controller
     * @param Comment $comment
     *
     * @return object
     */
    public function mapComment(Controller $controller, Comment $comment)
    {
        return (object)array(
            "postId"   => $comment->getId(),
            "posterId" => $comment->getPoster()->getId(),
            "poster"   => $comment->getPoster()->getDisplayName(),
            "message"  => $comment->getMessage(),
            "postDate" => $comment->getPostDate()->format(\DateTime::ISO8601),
            "links"    => (object)array(
                "update" => $controller->generateUrl('updateComment', array('commentId' => $comment->getId()), true)
            )
        );
    }
} 