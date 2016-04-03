<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ForumBundle\Controller;

use LaDanse\ForumBundle\Entity\Post;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
                "self"   => $controller->generateUrl('getPost', array('postId' => $post->getId()), true),
                "update" => $controller->generateUrl('updatePost', array('postId' => $post->getId()), true)
            )
        );
    }

    /**
     * @param Controller $controller
     * @param Post $post
     *
     * @return object
     */
    public function mapPostAndTopic(Controller $controller, Post $post)
    {
        $jsonPost = $this->mapPost($controller, $post);

        $topicMapper = new TopicMapper();
        $jsonForum = $topicMapper->mapTopicAndForum($controller, $post->getTopic());

        $jsonPost->topic = $jsonForum;

        return $jsonPost;
    }

    /**
     * @param Controller $controller
     * @param array $posts
     *
     * @return object
     */
    public function mapPostsAndTopic(Controller $controller, $posts)
    {
        $jsonPosts = array();

        /** @var Post $post */
        foreach($posts as $post)
        {
            $jsonPosts[] = $this->mapPostAndTopic($controller, $post);
        }

        return $jsonPosts;
    }
} 