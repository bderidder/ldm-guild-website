<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ForumBundle\Service;

use LaDanse\DomainBundle\Entity\Account;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use LaDanse\CommonBundle\Helper\LaDanseService;

use LaDanse\ForumBundle\Entity\Forum;
use LaDanse\ForumBundle\Entity\Topic;
use LaDanse\ForumBundle\Entity\Post;

use LaDanse\ForumBundle\Controller\ResourceHelper;

use LaDanse\ServicesBundle\Activity\ActivityEvent;
use LaDanse\ServicesBundle\Activity\ActivityType;

use JMS\DiExtraBundle\Annotation as DI;

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

        /** @var $forum \LaDanse\ForumBundle\Entity\Forum */
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
            $this->createSQLFromTemplate('LaDanseForumBundle::selectActivityForForums.sql.twig')
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
            $this->createSQLFromTemplate('LaDanseForumBundle::selectActivityForForum.sql.twig')
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

        /** @var $forum \LaDanse\ForumBundle\Entity\Forum */
        $forum = $topicRepo->find($forumId);

        if (null === $forum)
        {
            throw new ForumDoesNotExistException("Forum does not exist: " . $forumId);
        }
        else
        {
            $result = array();

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
            $result = array();

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

        $topicId = ResourceHelper::createUUID();

        $topic = new Topic();

        $topic->setId($topicId);
        $topic->setCreateDate(new \DateTime());
        $topic->setCreator($account);
        $topic->setSubject($subject);
        $topic->setForum($forum);

        $post = new Post();

        $post->setId(ResourceHelper::createUUID());
        $post->setPostDate(new \DateTime());
        $post->setPoster($account);
        $post->setMessage($text);
        $post->setTopic($topic);

        $topic->addPost($post);

        $em->persist($post);
        $em->persist($topic);
        $em->flush();

        $this->eventDispatcher->dispatch(
            ActivityEvent::EVENT_NAME,
            new ActivityEvent(
                ActivityType::FORUM_TOPIC_CREATE,
                $this->getAuthenticationService()->getCurrentContext()->getAccount(),
                array(
                    'postedBy' => array(
                        'id'   => $account->getId(),
                        'name' => $account->getDisplayName()
                    ),
                    'forumId'      => $forumId,
                    'topicSubject' => $subject,
                    'postMessage'  => $text,
                    'forumName'    => $forum->getName()
                )
            )
        );

        return $topicId;
    }

    /**
     * @param $topicId
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
                    array(
                        'removedBy' => array(
                            'id'   => $account->getId(),
                            'name' => $account->getDisplayName()
                        ),
                        'topicId'      => $topicId,
                        'topicSubject' => $topic->getSubject(),
                        'forumName'    => $topic->getForum()->getName()
                    )
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

        /* @var $topic \LaDanse\ForumBundle\Entity\Topic */
        $topic = $topicRepo->find($topicId);

        if (null === $topic)
        {
            throw new TopicDoesNotExistException("Topic does not exist: " . $topicId);
        }
        else
        {
            $post = new Post();

            $post->setId(ResourceHelper::createUUID());
            $post->setPostDate(new \DateTime());
            $post->setPoster($account);
            $post->setMessage($message);
            $post->setTopic($topic);

            $topic->addPost($post);

            $em->persist($post);
            $em->flush();

            $this->eventDispatcher->dispatch(
                ActivityEvent::EVENT_NAME,
                new ActivityEvent(
                    ActivityType::FORUM_POST_CREATE,
                    $this->getAuthenticationService()->getCurrentContext()->getAccount(),
                    array(
                        'postedBy' => array(
                            'id'   => $account->getId(),
                            'name' => $account->getDisplayName()
                        ),
                        'topicId'      => $topicId,
                        'topicSubject' => $topic->getSubject(),
                        'forumName'    => $topic->getForum()->getName(),
                        'message'      => $message
                    )
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
                    array(
                        'updatedBy' => array(
                            'id'   => $account->getId(),
                            'name' => $account->getDisplayName()
                        ),
                        'postId'       => $postId,
                        'topicSubject' => $post->getTopic()->getSubject(),
                        'forumName'    => $post->getTopic()->getForum()->getName(),
                        'oldMessage'   => $oldMessage,
                        'newMessage'   => $message
                    )
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
                    array(
                        'updatedBy' => array(
                            'id'   => $account->getId(),
                            'name' => $account->getDisplayName()
                        ),
                        'topicId'      => $topicId,
                        'topicSubject' => $topic->getSubject(),
                        'forumName'    => $topic->getForum()->getName(),
                        'oldMessage'   => $oldSubject,
                        'newMessage'   => $subject
                    )
                )
            );
        }
    }
}
