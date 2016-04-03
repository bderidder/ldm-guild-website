<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\CommentBundle\Service;

use LaDanse\CommentBundle\Entity\Comment;

use LaDanse\CommentBundle\Entity\CommentGroup;

use LaDanse\CommonBundle\Helper\LaDanseService;
use LaDanse\CommonBundle\Helper\ResourceHelper;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CommentService
 *
 * @package LaDanse\ForumBundle\Service
 */
class CommentService extends LaDanseService
{
    const SERVICE_NAME = 'LaDanse.CommentService';

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

    /**
     * @param $groupId
     *
     * @return CommentGroup
     *
     * @throws CommentGroupDoesNotExistException
     */
    public function getCommentGroup($groupId)
    {
        $doc = $this->getDoctrine();

        $groupRepo = $doc->getRepository(CommentGroup::REPOSITORY);

        $group = $groupRepo->find($groupId);

        if (null === $group)
        {
            throw new CommentGroupDoesNotExistException("CommentGroup does not exist: " . $groupId);
        }
        else
        {
            return $group;
        }
    }

    /**
     * @return string
     */
    public function createCommentGroup()
    {
        $doc = $this->getDoctrine();
        $em = $doc->getManager();

        $groupId = ResourceHelper::createUUID();
        
        $group = new CommentGroup();

        $group->setId($groupId);
        $group->setCreateDate(new \DateTime());

        $em->persist($group);
        $em->flush();

        return $groupId;
    }

    /**
     * @param $groupId
     *
     * @throws CommentGroupDoesNotExistException
     */
    public function removeCommentGroup($groupId)
    {
        $doc = $this->getDoctrine();
        $em = $doc->getManager();

        $groupRepo = $doc->getRepository(CommentGroup::REPOSITORY);

        $group = $groupRepo->find($groupId);

        if (null === $group)
        {
            throw new CommentGroupDoesNotExistException("CommentGroup does not exist: " . $groupId);
        }
        else
        {
            $em->remove($group);
            $em->flush();
        }
    }

    /**
     * @param $commentId
     *
     * @return Comment
     *
     * @throws CommentDoesNotExistException
     */
    public function getComment($commentId)
    {
        $doc = $this->getDoctrine();

        $commentRepo = $doc->getRepository(Comment::REPOSITORY);

        $comment = $commentRepo->find($commentId);

        if (null === $comment)
        {
            throw new CommentDoesNotExistException("Comment does not exist: " . $commentId);
        }
        else
        {
            return $comment;
        }
    }

    /**
     * @param $groupId
     * @param $account
     * @param $message
     * @throws CommentGroupDoesNotExistException
     */
    public function createComment($groupId, $account, $message)
    {
        $doc = $this->getDoctrine();

        $em = $doc->getManager();
        $groupRepo = $doc->getRepository(CommentGroup::REPOSITORY);

        /* @var $group \LaDanse\CommentBundle\Entity\CommentGroup */
        $group = $groupRepo->find($groupId);

        if (null === $group)
        {
            throw new CommentGroupDoesNotExistException("CommentGroup does not exist: " . $groupId);
        }
        else
        {
            $comment = new Comment();

            $comment->setId(ResourceHelper::createUUID());
            $comment->setPostDate(new \DateTime());
            $comment->setPoster($account);
            $comment->setMessage($message);
            $comment->setGroup($group);

            $group->addComment($comment);

            $em->persist($comment);
            $em->flush();
        }
    }

    /**
     * @param $commentId
     * @param $message
     * @throws CommentDoesNotExistException
     */
    public function updateComment($commentId, $message)
    {
        $doc = $this->getDoctrine();

        $em = $doc->getManager();
        $commentRepo = $doc->getRepository(Comment::REPOSITORY);

        $comment = $commentRepo->find($commentId);

        if (null === $comment)
        {
            throw new CommentDoesNotExistException("Post does not exist: " . $commentId);
        }
        else
        {
            $comment->setMessage($message);
            
            $em->persist($comment);
            $em->flush();
        }
    }
}
