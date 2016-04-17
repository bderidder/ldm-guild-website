<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\RestBundle\Controller\Forum;

use LaDanse\RestBundle\Controller\Forum\TopicMapper;
use LaDanse\DomainBundle\Entity\Forum\Forum;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ForumMapper
{
    public function mapForums(Controller $controller, $forums)
    {
        $jsonForums = [];

        /** @var Forum $forum */
        foreach($forums as $forum)
        {
            $jsonForums[] = $this->mapForum($controller, $forum);
        }

        $jsonObject = (object)array(
            "forums"  => $jsonForums,
            "links"   => (object)array(
                "self"        => $controller->generateUrl('getForumList', array(), true)
            )
        );

        return $jsonObject;
    }

    /**
     * @param Controller $controller
     * @param Forum $forum
     *
     * @return object
     */
    public function mapForum(Controller $controller, Forum $forum)
    {
        return (object)array(
            "forumId"        => $forum->getId(),
            "name"           => $forum->getName(),
            "description"    => $forum->getDescription(),
            "lastPost"       => $this->createLastPost($forum),
            "links"          => (object)array(
                "self"        => $controller->generateUrl('getForum', array('forumId' => $forum->getId()), true),
                "createTopic" => $controller->generateUrl('createTopic', array('forumId' => $forum->getId()), true)
            )
        );
    }

    /**
     * @param Controller $controller
     * @param Forum $forum
     *
     * @return object
     */
    public function mapForumAndTopics(Controller $controller, Forum $forum)
    {
        $topics = $forum->getTopics()->getValues();

        usort(
            $topics,
            function ($a, $b) {
                /** @var $a \LaDanse\DomainBundle\Entity\Forum\Topic */
                /** @var $b \LaDanse\DomainBundle\Entity\Forum\Topic */

                return $a->getCreateDate() < $b->getCreateDate();
            }
        );

        $topicMapper = new TopicMapper();

        $jsonArray = array();

        foreach ($topics as $topic)
        {
            $jsonArray[] = $topicMapper->mapTopic($controller, $topic);
        }

        $jsonForum = $this->mapForum($controller, $forum);

        $jsonForum->topics = $jsonArray;

        return $jsonForum;
    }

    private function createLastPost(Forum $forum)
    {
        if ($forum->getLastPostPoster() != null)
        {
            return (object)array(
                "date" => $forum->getLastPostDate()->format(\DateTime::ISO8601),
                "topic" => (object)array(
                    "id" => $forum->getLastPostTopic()->getId(),
                    "subject" => $forum->getLastPostTopic()->getSubject()
                ),
                "poster" => (object)array(
                    "id" => $forum->getLastPostPoster()->getId(),
                    "displayName" => $forum->getLastPostPoster()->getDisplayName()
                )
            );
        }
        else
        {
            return null;
        }
    }
} 