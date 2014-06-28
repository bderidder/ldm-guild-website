<?php

namespace LaDanse\ForumBundle\Service;

use Symfony\Component\DependencyInjection\ContainerAware,
    Symfony\Component\DependencyInjection\ContainerInterface;

use LaDanse\CommonBundle\Helper\LaDanseService;

use LaDanse\ForumBundle\Entity\Forum,
    LaDanse\ForumBundle\Entity\Topic,
    LaDanse\ForumBundle\Entity\Post,
    LaDanse\DomainBundle\Entity\Account;

use LaDanse\ForumBundle\Controller\ResourceHelper;

class ForumService extends LaDanseService
{
    const SERVICE_NAME = 'LaDanse.ForumService';

	public function __construct(ContainerInterface $container)
	{
		parent::__construct($container);
	}

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

    public function getPost($postId)
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
            return $post;
        }
    }

    public function createTopic($account, $subject)
    {
        $doc = $this->getDoctrine();
        $em = $doc->getManager();

        $topicId = ResourceHelper::createUUID();
        
        $topic = new Topic();

        $topic->setId($topicId);
        $topic->setCreateDate(new \DateTime());
        $topic->setCreator($account);
        $topic->setSubject($subject);

        $em->persist($topic);
        $em->flush();

        return $topicId;
    }

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

    public function createPost($topicId, $account, $message)
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
}