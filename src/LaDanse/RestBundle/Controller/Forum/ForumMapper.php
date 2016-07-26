<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\RestBundle\Controller\Forum;

use LaDanse\DomainBundle\Entity\Forum\Forum;
use LaDanse\RestBundle\Controller\Forum\TopicMapper;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ForumMapper
{
    public function mapForums(UrlGeneratorInterface $generator, $forums)
    {
        $jsonForums = [];

        /** @var Forum $forum */
        foreach($forums as $forum)
        {
            $jsonForums[] = $this->mapForum($generator, $forum);
        }

        $jsonObject = (object)array(
            "forums"  => $jsonForums,
            "links"   => (object)array(
                "self"        => $generator->generate('getForumList', array(), UrlGeneratorInterface::ABSOLUTE_URL)
            )
        );

        return $jsonObject;
    }

    /**
     * @param UrlGeneratorInterface $generator
     * @param Forum $forum
     *
     * @return object
     */
    public function mapForum(UrlGeneratorInterface $generator, Forum $forum)
    {
        return (object)array(
            "forumId"        => $forum->getId(),
            "name"           => $forum->getName(),
            "description"    => $forum->getDescription(),
            "lastPost"       => $this->createLastPost($forum),
            "links"          => (object)array(
                "self"        => $generator->generate('getForum', array('forumId' => $forum->getId()), UrlGeneratorInterface::ABSOLUTE_URL),
                "createTopic" => $generator->generate('createTopic', array('forumId' => $forum->getId()), UrlGeneratorInterface::ABSOLUTE_URL)
            )
        );
    }

    /**
     * @param UrlGeneratorInterface $generator
     * @param Forum $forum
     *
     * @return object
     */
    public function mapForumAndTopics(UrlGeneratorInterface $generator, Forum $forum)
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
            $jsonArray[] = $topicMapper->mapTopic($generator, $topic);
        }

        $jsonForum = $this->mapForum($generator, $forum);

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