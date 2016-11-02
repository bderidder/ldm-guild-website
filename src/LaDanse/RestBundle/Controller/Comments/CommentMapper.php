<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\RestBundle\Controller\Comments;

use LaDanse\DomainBundle\Entity\Comments\Comment;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class CommentMapper
 *
 * @package LaDanse\ForumBundle\Controller
 */
class CommentMapper
{
    /**
     * @param UrlGeneratorInterface $generator
     * @param Comment $comment
     *
     * @return object
     */
    public function mapComment(UrlGeneratorInterface $generator, Comment $comment)
    {
        return (object)[
            "postId"   => $comment->getId(),
            "posterId" => $comment->getPoster()->getId(),
            "poster"   => $comment->getPoster()->getDisplayName(),
            "message"  => $comment->getMessage(),
            "postDate" => $comment->getPostDate()->format(\DateTime::ISO8601),
            "links"    => (object)[
                "update" => $generator->generate('updateComment', ['commentId' => $comment->getId()], UrlGeneratorInterface::ABSOLUTE_URL)
            ]
        ];
    }
} 