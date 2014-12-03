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

                return $a->getCreateDate() > $b->getCreateDate();
            }
        );

        $topicMapper = new TopicMapper();

        $jsonArray = array();

        foreach ($topics as $topic)
        {
            $jsonArray[] = $topicMapper->mapTopic($controller, $topic);
        }

        return (object)array(
            "topicId" => $forum->getId(),
            "name"    => $forum->getName(),
            "posts"   => $jsonArray,
            "links"   => (object)array(
                "self" => $controller->generateUrl('getForum', array('forumId' => $forum->getId()), true)
            )
        );
    }
} 