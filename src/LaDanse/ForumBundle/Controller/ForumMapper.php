<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ForumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use LaDanse\ForumBundle\Entity\Forum;

class ForumMapper
{
    public function mapForums(Controller $controller, $forums)
    {
        $jsonForums = [];

        /** @var Forum $forum */
        foreach($forums as $forum)
        {
            $jsonForums[] = (object)array(
                "forumId" => $forum->getId(),
                "name"    => $forum->getName(),
                "links"   => (object)array(
                    "self"        => $controller->generateUrl('getForum', array('forumId' => $forum->getId()), true)
                )
            );
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
    public function mapForumAndTopics(Controller $controller, Forum $forum)
    {
        $topics = $forum->getTopics()->getValues();

        usort(
            $topics,
            function ($a, $b) {
                /** @var $a \LaDanse\ForumBundle\Entity\Topic */
                /** @var $b \LaDanse\ForumBundle\Entity\Topic */

                return $a->getCreateDate() < $b->getCreateDate();
            }
        );

        $topicMapper = new TopicMapper();

        $jsonArray = array();

        foreach ($topics as $topic)
        {
            $jsonArray[] = $topicMapper->mapTopic($controller, $topic);
        }

        return (object)array(
            "forumId" => $forum->getId(),
            "name"    => $forum->getName(),
            "topics"  => $jsonArray,
            "links"   => (object)array(
                "self"        => $controller->generateUrl('getForum', array('forumId' => $forum->getId()), true),
                "createTopic" => $controller->generateUrl('createTopic', array('forumId' => $forum->getId()), true)
            )
        );
    }
} 