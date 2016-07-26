<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\RestBundle\Controller\Forum;

use LaDanse\DomainBundle\Entity\Forum\Topic;

use LaDanse\RestBundle\Controller\Forum\ForumMapper;
use LaDanse\RestBundle\Controller\Forum\PostMapper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class TopicMapper
 *
 * @package LaDanse\ForumBundle\Controller
 */
class TopicMapper
{
    /**
     * @param UrlGeneratorInterface $generator
     * @param Topic $topic
     *
     * @return object
     */
    public function mapTopic(UrlGeneratorInterface $generator, Topic $topic)
    {
        return (object)array(
            "topicId"    => $topic->getId(),
            "creatorId"  => $topic->getCreator()->getId(),
            "creator"    => $topic->getCreator()->getDisplayName(),
            "subject"    => $topic->getSubject(),
            "createDate" => $topic->getCreateDate()->format(\DateTime::ISO8601),
            "lastPost"   => $this->createLastPost($topic),
            "links"      => (object)array(
                "self"
                    => $generator->generate('getPostsInTopic', array('topicId' => $topic->getId()), UrlGeneratorInterface::ABSOLUTE_URL),
                "createPostInTopic"
                    => $generator->generate('createPostInTopic', array('topicId' => $topic->getId()), UrlGeneratorInterface::ABSOLUTE_URL)
            )
        );
    }

    /**
     * @param UrlGeneratorInterface $generator
     * @param Topic $topic
     *
     * @return object
     */
    public function mapTopicAndForum(UrlGeneratorInterface $generator, Topic $topic)
    {
        $jsonTopic = $this->mapTopic($generator, $topic);

        $forumMapper = new ForumMapper();
        $jsonForum = $forumMapper->mapForum($generator, $topic->getForum());

        $jsonTopic->forum = $jsonForum;

        return $jsonTopic;
    }

    /**
     * @param UrlGeneratorInterface $generator
     * @param array $topics
     *
     * @return array
     */
    public function mapTopicsAndForum(UrlGeneratorInterface $generator, $topics)
    {
        $jsonTopics = array();

        /** @var Topic $topic */
        foreach($topics as $topic)
        {
            $jsonTopics[] = $this->mapTopicAndForum($generator, $topic);
        }

        return $jsonTopics;
    }

    /**
     * @param UrlGeneratorInterface $generator
     * @param Topic $topic
     *
     * @return object
     */
    public function mapTopicAndPosts(UrlGeneratorInterface $generator, Topic $topic)
    {
        $topicObject = $this->mapTopic($generator, $topic);

        $posts = $topic->getPosts()->getValues();

        usort(
            $posts,
            function ($a, $b) {
                /** @var $a \LaDanse\DomainBundle\Entity\Forum\Post */
                /** @var $b \LaDanse\DomainBundle\Entity\Forum\Post */

                return $a->getPostDate() > $b->getPostDate();
            }
        );

        $postMapper = new PostMapper();

        $jsonArray = array();

        foreach ($posts as $post)
        {
            $jsonArray[] = $postMapper->mapPost($generator, $post);
        }

        $topicObject->posts = $jsonArray;

        return $topicObject;
    }

    private function createLastPost(Topic $topic)
    {
        if ($topic->getLastPostPoster() != null)
        {
            return (object)array(
                "date" => $topic->getLastPostDate()->format(\DateTime::ISO8601),
                "poster" => (object)array(
                    "id" => $topic->getLastPostPoster()->getId(),
                    "displayName" => $topic->getLastPostPoster()->getDisplayName()
                )
            );
        }
        else
        {
            return null;
        }
    }
} 