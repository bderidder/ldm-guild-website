<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\RestBundle\Controller\Forum;

use LaDanse\DomainBundle\Entity\Forum\Post;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class PostMapper
 *
 * @package LaDanse\ForumBundle\Controller
 */
class PostMapper
{
    /**
     * @param UrlGeneratorInterface $generator
     * @param Post $post
     *
     * @return object
     */
    public function mapPost(UrlGeneratorInterface $generator, Post $post)
    {
        return (object)array(
            "postId"   => $post->getId(),
            "posterId" => $post->getPoster()->getId(),
            "poster"   => $post->getPoster()->getDisplayName(),
            "message"  => $post->getMessage(),
            "postDate" => $post->getPostDate()->format(\DateTime::ISO8601),
            "links"    => (object)array(
                "self"   => $generator->generate('getPost', array('postId' => $post->getId()), UrlGeneratorInterface::ABSOLUTE_URL),
                "update" => $generator->generate('updatePost', array('postId' => $post->getId()), UrlGeneratorInterface::ABSOLUTE_URL)
            )
        );
    }

    /**
     * @param UrlGeneratorInterface $generator
     * @param Post $post
     *
     * @return object
     */
    public function mapPostAndTopic(UrlGeneratorInterface $generator, Post $post)
    {
        $jsonPost = $this->mapPost($generator, $post);

        $topicMapper = new TopicMapper();
        $jsonForum = $topicMapper->mapTopicAndForum($generator, $post->getTopic());

        $jsonPost->topic = $jsonForum;

        return $jsonPost;
    }

    /**
     * @param UrlGeneratorInterface $generator
     * @param array $posts
     *
     * @return object
     */
    public function mapPostsAndTopic(UrlGeneratorInterface $generator, $posts)
    {
        $jsonPosts = array();

        /** @var Post $post */
        foreach($posts as $post)
        {
            $jsonPosts[] = $this->mapPostAndTopic($generator, $post);
        }

        return $jsonPosts;
    }
} 