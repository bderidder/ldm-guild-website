<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ForumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use LaDanse\ForumBundle\Entity\Post;

/**
 * Class PostMapper
 *
 * @package LaDanse\ForumBundle\Controller
 */
class PostMapper
{
    /**
     * @param Controller $controller
     * @param Post $post
     *
     * @return object
     */
    public function mapPost(Controller $controller, Post $post)
    {
        return (object)array(
            "postId"   => $post->getId(),
            "posterId" => $post->getPoster()->getId(),
            "poster"   => $post->getPoster()->getDisplayName(),
            "message"  => $post->getMessage(),
            "postDate" => $post->getPostDate()->format(\DateTime::ISO8601),
            "links"    => (object)array(
                "self" => $controller->generateUrl('getPost', array('postId' => $post->getId()), true),
            )
        );
    }
} 