<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ForumBundle\Controller;

use LaDanse\ForumBundle\Entity\Topic;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class TopicMapper
 *
 * @package LaDanse\ForumBundle\Controller
 */
class TopicMapper
{
    /**
     * @param Controller $controller
     * @param Topic $topic
     *
     * @return object
     */
    public function mapTopic(Controller $controller, Topic $topic)
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
                    => $controller->generateUrl('getPostsInTopic', array('topicId' => $topic->getId()), true),
                "createPostInTopic"
                    => $controller->generateUrl('createPostInTopic', array('topicId' => $topic->getId()), true)
            )
        );
    }

    /**
     * @param Controller $controller
     * @param Topic $topic
     *
     * @return object
     */
    public function mapTopicAndForum(Controller $controller, Topic $topic)
    {
        $jsonTopic = $this->mapTopic($controller, $topic);

        $forumMapper = new ForumMapper();
        $jsonForum = $forumMapper->mapForum($controller, $topic->getForum());

        $jsonTopic->forum = $jsonForum;

        return $jsonTopic;
    }

    /**
     * @param Controller $controller
     * @param array $topics
     *
     * @return object
     */
    public function mapTopicsAndForum(Controller $controller, $topics)
    {
        $jsonTopics = array();

        /** @var Topic $topic */
        foreach($topics as $topic)
        {
            $jsonTopics[] = $this->mapTopicAndForum($controller, $topic);
        }

        return $jsonTopics;
    }

    /**
     * @param Controller $controller
     * @param Topic $topic
     *
     * @return object
     */
    public function mapTopicAndPosts(Controller $controller, Topic $topic)
    {
        $topicObject = $this->mapTopic($controller, $topic);

        $posts = $topic->getPosts()->getValues();

        usort(
            $posts,
            function ($a, $b) {
                /** @var $a \LaDanse\ForumBundle\Entity\Post */
                /** @var $b \LaDanse\ForumBundle\Entity\Post */

                return $a->getPostDate() > $b->getPostDate();
            }
        );

        $postMapper = new PostMapper();

        $jsonArray = array();

        foreach ($posts as $post)
        {
            $jsonArray[] = $postMapper->mapPost($controller, $post);
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