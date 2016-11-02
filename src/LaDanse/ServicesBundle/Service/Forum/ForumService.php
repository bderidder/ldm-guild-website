<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Service\Forum;

use JMS\DiExtraBundle\Annotation as DI;
use LaDanse\DomainBundle\Entity\Account;
use LaDanse\DomainBundle\Entity\Forum\Forum;
use LaDanse\DomainBundle\Entity\Forum\Post;
use LaDanse\DomainBundle\Entity\Forum\Topic;
use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;
use LaDanse\ServicesBundle\Common\LaDanseService;
use LaDanse\ServicesBundle\Common\UUIDUtils;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class ForumService
 *
 * @package LaDanse\ForumBundle\Service
 *
 * @DI\Service(ForumService::SERVICE_NAME, public=true)
 */
class ForumService extends LaDanseService
{
    const SERVICE_NAME = 'LaDanse.ForumService';

    /**
     * @var $eventDispatcher EventDispatcherInterface
     * @DI\Inject("event_dispatcher")
     */
    public $eventDispatcher;

    /**
     * @param ContainerInterface $container
     *
     * @DI\InjectParams({
     *     "container" = @DI\Inject("service_container")
     * })
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

    /**
     * @return array
     */
    public function getAllForums()
    {
        $doc = $this->getDoctrine();

        $forumRepo = $doc->getRepository(Forum::REPOSITORY);

        return $forumRepo->findAll();
    }

    /**
     * @param $forumId
     *
     * @return Forum
     *
     * @throws ForumDoesNotExistException
     */
    public function getForum($forumId)
    {
        $doc = $this->getDoctrine();

        $forumRepo = $doc->getRepository(Forum::REPOSITORY);

        /** @var $forum \LaDanse\DomainBundle\Entity\Forum\Forum */
        $forum = $forumRepo->find($forumId);

        if (null === $forum)
        {
            throw new ForumDoesNotExistException("Forum does not exist: " . $forumId);
        }
        else
        {
            return $forum;
        }
    }

    /**
     * @return array
     */
    public function getActivityForForums()
    {
        $em = $this->getDoctrine()->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery(
            $this->createSQLFromTemplate('LaDanseDomainBundle::forum\selectActivityForForums.sql.twig')
        );
        $query->setMaxResults(10);

        return $query->getResult();
    }

    /**
     * @param $forumId
     *
     * @return array
     */
    public function getActivityForForum($forumId)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery(
            $this->createSQLFromTemplate('LaDanseDomainBundle::forum\selectActivityForForum.sql.twig')
        );
        $query->setParameter('forumId', $forumId);
        $query->setMaxResults(10);

        return $query->getResult();
    }

    /**
     * @param $forumId
     *
     * @return array
     *
     * @throws ForumDoesNotExistException
     */
    public function getAllTopicsInForum($forumId)
    {
        $doc = $this->getDoctrine();

        $topicRepo = $doc->getRepository(Forum::REPOSITORY);

        /** @var $forum \LaDanse\DomainBundle\Entity\Forum\Forum */
        $forum = $topicRepo->find($forumId);

        if (null === $forum)
        {
            throw new ForumDoesNotExistException("Forum does not exist: " . $forumId);
        }
        else
        {
            $result = [];

            $topics = $forum->getTopics();

            foreach($topics as $topic)
            {
                $result[] = $topic;
            }

            return $result;
        }
    }

    /**
     * @param $topicId
     * @return array
     * @throws TopicDoesNotExistException
     */
    public function getAllPosts($topicId)
    {
        $doc = $this->getDoctrine();

        $topicRepo = $doc->getRepository(Topic::REPOSITORY);

        $topic = $topicRepo->find($topicId);

        if (null === $topic)
        {
            throw new TopicDoesNotExistException("Topic does not exist: " . $topicId);
        }
        else
        {
            $result = [];

            $posts = $topic->getPosts();

            foreach($posts as $post)
            {
                $result[] = $post;
            }

            return $result;
        }
    }

    /**
     * @param $postId
     * @return Post
     * @throws PostDoesNotExistException
     */
    public function getPost($postId)
    {
        $doc = $this->getDoctrine();

        $postRepo = $doc->getRepository(Post::REPOSITORY);

        $post = $postRepo->find($postId);

        if (null === $post)
        {
            throw new PostDoesNotExistException("Post does not exist: " . $postId);
        }
        else
        {
            return $post;
        }
    }

    /**
     * @param $topicId
     * @return Topic
     * @throws TopicDoesNotExistException
     */
    public function getTopic($topicId)
    {
        $doc = $this->getDoctrine();

        $topicRepo = $doc->getRepository(Topic::REPOSITORY);

        $topic = $topicRepo->find($topicId);

        if (null === $topic)
        {
            throw new TopicDoesNotExistException("Topic does not exist: " . $topicId);
        }
        else
        {
            return $topic;
        }
    }

    /**
     * @param Account $account
     * @param $forumId
     * @param $subject
     * @param $text
     *
     * @return string
     *
     * @throws ForumDoesNotExistException
     */
    public function createTopicInForum(Account $account, $forumId, $subject, $text)
    {
        $doc = $this->getDoctrine();
        $em = $doc->getManager();

        $forum = $this->getForum($forumId);

        $topicId = UUIDUtils::createUUID();

        $topic = new Topic();

        $topic->setId($topicId);
        $topic->setCreateDate(new \DateTime());
        $topic->setCreator($account);
        $topic->setSubject($subject);
        $topic->setForum($forum);

        $post = new Post();

        $post->setId(UUIDUtils::createUUID());
        $post->setPostDate(new \DateTime());
        $post->setPoster($account);
        $post->setMessage($text);
        $post->setTopic($topic);

        $topic->addPost($post);

        // update last post on Forum
        $forum->setLastPostDate($post->getPostDate());
        $forum->setLastPostPoster($account);
        $forum->setLastPostTopic($topic);

        // update last post on Topic
        $topic->setLastPostDate($post->getPostDate());
        $topic->setLastPostPoster($account);

        $em->persist($post);
        $em->persist($topic);
        $em->flush();

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::FORUM_TOPIC_CREATE,
                $this->getAuthenticationService()->getCurrentContext()->getAccount(),
                [
                    'postedBy' =>
                    [
                        'id'   => $account->getId(),
                        'name' => $account->getDisplayName()
                    ],
                    'topicId'      => $topicId,
                    'topicSubject' => $subject,
                    'message'      => $text,
                    'forumId'      => $forum->getId(),
                    'forumName'    => $forum->getName()
                ]
            )
        );

        return $topicId;
    }

    /**
     * @param Account $account
     * @param string $topicId
     *
     * @throws TopicDoesNotExistException
     */
    public function removeTopic(Account $account, $topicId)
    {
        $doc = $this->getDoctrine();
        $em = $doc->getManager();

        $topicRepo = $doc->getRepository(Topic::REPOSITORY);

        $topic = $topicRepo->find($topicId);

        if (null === $topic)
        {
            throw new TopicDoesNotExistException("Topic does not exist: " . $topicId);
        }
        else
        {
            $em->remove($topic);
            $em->flush();

            $this->eventDispatcher->dispatch(
                ActivityEvent::EVENT_NAME,
                new ActivityEvent(
                    ActivityType::FORUM_TOPIC_REMOVE,
                    $this->getAuthenticationService()->getCurrentContext()->getAccount(),
                    [
                        'removedBy' =>
                        [
                            'id'   => $account->getId(),
                            'name' => $account->getDisplayName()
                        ],
                        'topicId'      => $topicId,
                        'topicSubject' => $topic->getSubject(),
                        'forumId'      => $topic->getForum()->getId(),
                        'forumName'    => $topic->getForum()->getName()
                    ]
                )
            );
        }
    }

    /**
     * @param Account $account
     * @param $topicId
     * @param $message
     * @throws TopicDoesNotExistException
     */
    public function createPost(Account $account, $topicId, $message)
    {
        $doc = $this->getDoctrine();

        $em = $doc->getManager();
        $topicRepo = $doc->getRepository(Topic::REPOSITORY);

        /* @var $topic \LaDanse\DomainBundle\Entity\Forum\Topic */
        $topic = $topicRepo->find($topicId);

        if (null === $topic)
        {
            throw new TopicDoesNotExistException("Topic does not exist: " . $topicId);
        }
        else
        {
            $post = new Post();

            $post->setId(UUIDUtils::createUUID());
            $post->setPostDate(new \DateTime());
            $post->setPoster($account);
            $post->setMessage($message);
            $post->setTopic($topic);

            $topic->addPost($post);

            // update last post on Forum
            $forum = $topic->getForum();
            $forum->setLastPostDate($post->getPostDate());
            $forum->setLastPostPoster($account);
            $forum->setLastPostTopic($topic);

            // update last post on Topic
            $topic->setLastPostDate($post->getPostDate());
            $topic->setLastPostPoster($account);

            $em->persist($post);
            $em->flush();

            $this->eventDispatcher->dispatch(
                ActivityEvent::EVENT_NAME,
                new ActivityEvent(
                    ActivityType::FORUM_POST_CREATE,
                    $this->getAuthenticationService()->getCurrentContext()->getAccount(),
                    [
                        'postedBy' =>
                        [
                            'id'   => $account->getId(),
                            'name' => $account->getDisplayName()
                        ],
                        'topicId'      => $topicId,
                        'topicSubject' => $topic->getSubject(),
                        'message'      => $message,
                        'forumId'      => $topic->getForum()->getId(),
                        'forumName'    => $topic->getForum()->getName()
                    ]
                )
            );
        }
    }

    /**
     * @param Account $account
     * @param $postId
     * @param $message
     * @throws PostDoesNotExistException
     */
    public function updatePost(Account $account, $postId, $message)
    {
        $doc = $this->getDoctrine();

        $em = $doc->getManager();
        $postRepo = $doc->getRepository(Post::REPOSITORY);

        $post = $postRepo->find($postId);

        $oldMessage = $post->getMessage();

        if (null === $post)
        {
            throw new PostDoesNotExistException("Post does not exist: " . $postId);
        }
        else
        {
            $post->setMessage($message);
            
            $em->persist($post);
            $em->flush();

            $this->eventDispatcher->dispatch(
                ActivityEvent::EVENT_NAME,
                new ActivityEvent(
                    ActivityType::FORUM_POST_UPDATE,
                    $this->getAuthenticationService()->getCurrentContext()->getAccount(),
                    [
                        'updatedBy' =>
                        [
                            'id'   => $account->getId(),
                            'name' => $account->getDisplayName()
                        ],
                        'postId'       => $postId,
                        'topicId'      => $post->getTopic()->getId(),
                        'topicSubject' => $post->getTopic()->getSubject(),
                        'forumId'      => $post->getTopic()->getForum()->getId(),
                        'forumName'    => $post->getTopic()->getForum()->getName(),
                        'oldMessage'   => $oldMessage,
                        'newMessage'   => $message
                    ]
                )
            );
        }
    }

    /**
     * @param Account $account
     * @param $topicId
     * @param $subject
     * @throws TopicDoesNotExistException
     */
    public function updateTopic(Account $account, $topicId, $subject)
    {
        $doc = $this->getDoctrine();

        $em = $doc->getManager();
        $topicRepo = $doc->getRepository(Topic::REPOSITORY);

        $topic = $topicRepo->find($topicId);

        $oldSubject = $topic->getSubject();

        if (null === $topic)
        {
            throw new TopicDoesNotExistException("Topic does not exist: " . $topicId);
        }
        else
        {
            $topic->setSubject($subject);

            $em->persist($topic);
            $em->flush();

            $this->eventDispatcher->dispatch(
                ActivityEvent::EVENT_NAME,
                new ActivityEvent(
                    ActivityType::FORUM_TOPIC_UPDATE,
                    $this->getAuthenticationService()->getCurrentContext()->getAccount(),
                    [
                        'updatedBy' =>
                        [
                            'id'   => $account->getId(),
                            'name' => $account->getDisplayName()
                        ],
                        'topicId'      => $topicId,
                        'topicSubject' => $topic->getSubject(),
                        'forumId'      => $topic->getForum()->getId(),
                        'forumName'    => $topic->getForum()->getName(),
                        'oldMessage'   => $oldSubject,
                        'newMessage'   => $subject
                    ]
                )
            );
        }
    }

    /**
     * Update all last posts
     */
    public function updateLastPosts()
    {
        $doc = $this->getDoctrine();
        $em = $doc->getManager();

        $forums = $this->getAllForums();

        /** @var Forum $forum */
        foreach($forums as $forum)
        {
            $topics = $forum->getTopics();

            /** @var Post $lastPostInForum */
            $lastPostInForum = null;

            /** @var Topic $topic */
            foreach($topics as $topic)
            {
                /** @var Post $lastPostInTopic */
                $lastPostInTopic = null;

                $posts = $topic->getPosts();

                /** @var Post $post */
                foreach($posts as $post)
                {
                    // Update $lastPostInTopic
                    if ($lastPostInTopic == null)
                    {
                        $lastPostInTopic = $post;
                    }
                    else if ($post->getPostDate() > $lastPostInTopic->getPostDate())
                    {
                        $lastPostInTopic = $post;
                    }

                    // Update $lastPostInForum
                    if ($lastPostInForum == null)
                    {
                        $lastPostInForum = $post;
                    }
                    else if ($post->getPostDate() > $lastPostInForum->getPostDate())
                    {
                        $lastPostInForum = $post;
                    }
                }

                // Update $lastPostInTopic
                if ($lastPostInTopic != null)
                {
                    $topic->setLastPostDate($lastPostInTopic->getPostDate());
                    $topic->setLastPostPoster($lastPostInTopic->getPoster());
                }
            }

            // Update $lastPostInTopic
            if ($lastPostInForum != null)
            {
                $forum->setLastPostDate($lastPostInForum->getPostDate());
                $forum->setLastPostPoster($lastPostInForum->getPoster());
                $forum->setLastPostTopic($lastPostInForum->getTopic());
            }
        }

        $em->flush();
    }
}
