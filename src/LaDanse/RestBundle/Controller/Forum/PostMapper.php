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
        return (object)[
            "postId"   => $post->getId(),
            "posterId" => $post->getPoster()->getId(),
            "poster"   => $post->getPoster()->getDisplayName(),
            "message"  => $post->getMessage(),
            "postDate" => $post->getPostDate()->format(\DateTime::ISO8601),
            "links"    => (object)[
                "self"   => $generator->generate('getPost', ['postId' => $post->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
                "update" => $generator->generate('updatePost', ['postId' => $post->getId()], UrlGeneratorInterface::ABSOLUTE_URL)
            ]
        ];
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
     * @return array
     */
    public function mapPostsAndTopic(UrlGeneratorInterface $generator, $posts)
    {
        $jsonPosts = [];

        /** @var Post $post */
        foreach($posts as $post)
        {
            $jsonPosts[] = $this->mapPostAndTopic($generator, $post);
        }

        return $jsonPosts;
    }
} 