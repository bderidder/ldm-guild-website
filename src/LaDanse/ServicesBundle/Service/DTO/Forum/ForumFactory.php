<?php

namespace LaDanse\ServicesBundle\Service\DTO\Forum;

use LaDanse\DomainBundle\Entity as DomainEntity;
use LaDanse\ForumBundle\Entity as ForumEntity;
use LaDanse\ServicesBundle\Service\DTO\Reference\AccountReference;

class ForumFactory
{
    public static function create(ForumEntity\Forum $forum)
    {
        $factory = new ForumFactory();

        return $factory->createForum($forum);
    }

    private function createForum(ForumEntity\Forum $forum)
    {
        return new Forum(
            $forum->getId(),
            $forum->getName(),
            $forum->getDescription(),
            $this->createTopicEntries($forum));
    }

    private Function createTopicEntries(ForumEntity\Forum $forum)
    {
        $topicEntries = array();

        /** @var ForumEntity\Topic $topic */
        foreach($forum->getTopics() as $topic)
        {
            $topicEntries[] = new TopicEntry(
                $topic->getId(),
                $topic->getSubject(),
                $topic->getCreateDate(),
                new AccountReference(
                    $topic->getCreator()->getId(),
                    $topic->getCreator()->getDisplayName()),
                new LastPostEntry(
                    $topic->getLastPostDate(),
                    new AccountReference(
                        $topic->getLastPostPoster()->getId(),
                        $topic->getLastPostPoster()->getDisplayName())
                ));
        }

        return $topicEntries;
    }
}