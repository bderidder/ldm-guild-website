<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ForumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use LaDanse\ForumBundle\Entity\Topic;

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
            "links"      => (object)array(
                "self" => $controller->generateUrl('getPosts', array('topicId' => $topic->getId()), true)
            )
        );
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
} 