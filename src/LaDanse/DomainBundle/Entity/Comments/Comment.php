<?php

namespace LaDanse\DomainBundle\Entity\Comments;

use Doctrine\ORM\Mapping as ORM;
use LaDanse\DomainBundle\Entity\Account;

/**
 * Post
 *
 * @ORM\Table(name="Comment")
 * @ORM\Entity
 */
class Comment
{
    const REPOSITORY = 'LaDanseDomainBundle:Comments\Comment';

    /**
     * @var integer
     *
     * @ORM\Column(name="commentId", type="guid")
     * @ORM\Id
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="postDate", type="datetime")
     */
    private $postDate;

    /**
     * @ORM\ManyToOne(targetEntity="LaDanse\DomainBundle\Entity\Account")
     * @ORM\JoinColumn(name="posterId", referencedColumnName="id", nullable=true)
     */
    private $poster;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text")
     */
    private $message;

    /**
     * @ORM\ManyToOne(targetEntity="LaDanse\DomainBundle\Entity\Comments\CommentGroup", inversedBy="comments")
     * @ORM\JoinColumn(name="groupId", referencedColumnName="groupId", nullable=true)
     */
    private $group;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set postDate
     *
     * @param \DateTime $postDate
     * @return Comment
     */
    public function setPostDate($postDate)
    {
        $this->postDate = $postDate;

        return $this;
    }

    /**
     * Get postDate
     *
     * @return \DateTime 
     */
    public function getPostDate()
    {
        return $this->postDate;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return Comment
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set poster
     *
     * @param Account $poster
     * @return Comment
     */
    public function setPoster(Account $poster = null)
    {
        $this->poster = $poster;

        return $this;
    }

    /**
     * Get poster
     *
     * @return Account
     */
    public function getPoster()
    {
        return $this->poster;
    }

    /**
     * Set CommentGroup
     *
     * @param CommentGroup $group
     * @return Comment
     */
    public function setGroup(CommentGroup $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get CommentGroup
     *
     * @return \LaDanse\DomainBundle\Entity\Comments\CommentGroup
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return Comment
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
