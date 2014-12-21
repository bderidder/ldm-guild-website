<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ForumBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

use LaDanse\CommonBundle\Helper\LaDanseService;

use LaDanse\ForumBundle\Entity\Forum;
use LaDanse\ForumBundle\Entity\Topic;
use LaDanse\ForumBundle\Entity\Post;

use LaDanse\ForumBundle\Controller\ResourceHelper;

/**
 * Class ForumService
 *
 * @package LaDanse\ForumBundle\Service
 */
class ForumService extends LaDanseService
{
    const SERVICE_NAME = 'LaDanse.ForumService';

    /**
     * @param ContainerInterface $container
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
     * @param $account
     * @param $forumId
     * @param $subject
     *
     * @return string
     *
     * @throws ForumDoesNotExistException
     */
    public function createTopicInForum($account, $forumId, $subject, $text)
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

        $em->persist($topic);
        $em->flush();

        $this->createPost($topicId, $account, $text);

        return $topicId;
    }

    /**
     * @param $topicId
     * @throws TopicDoesNotExistException
     */
    public function removeTopic($topicId)
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
        }
    }

    /**
     * @param $topicId
     * @param $account
     * @param $message
     * @throws TopicDoesNotExistException
     */
    public function createPost($topicId, $account, $message)
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
        }
    }

    /**
     * @param $postId
     * @param $message
     * @throws PostDoesNotExistException
     */
    public function updatePost($postId, $message)
    {
        $doc = $this->getDoctrine();

        $em = $doc->getManager();
        $postRepo = $doc->getRepository(Post::REPOSITORY);

        $post = $postRepo->find($postId);

        if (null === $post)
        {
            throw new PostDoesNotExistException("Post does not exist: " . $postId);
        }
        else
        {
            $post->setMessage($message);
            
            $em->persist($post);
            $em->flush();
        }
    }

    /**
     * @param $topicId
     * @param $subject
     * @throws TopicDoesNotExistException
     */
    public function updateTopic($topicId, $subject)
    {
        $doc = $this->getDoctrine();

        $em = $doc->getManager();
        $postRepo = $doc->getRepository(Topic::REPOSITORY);

        $post = $postRepo->find($topicId);

        if (null === $post)
        {
            throw new TopicDoesNotExistException("Topic does not exist: " . $topicId);
        }
        else
        {
            $post->setSubject($subject);

            $em->persist($post);
            $em->flush();
        }
    }
}
