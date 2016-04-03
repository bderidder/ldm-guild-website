<?php

namespace LaDanse\CommentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;


/**
 * Topic
 *
 * @ORM\Table(name="CommentGroup")
 * @ORM\Entity
 */
class CommentGroup
{
    const REPOSITORY = 'LaDanseCommentBundle:CommentGroup';

    /**
     * @var integer
     *
     * @ORM\Column(name="groupId", type="guid")
     * @ORM\Id
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="postDate", type="datetime")
     */
    private $createDate;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="group", cascade={"persist", "remove"})
     */
    protected $comments;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

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
     * Set createDate
     *
     * @param \DateTime $createDate
     * @return CommentGroup
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;

        return $this;
    }

    /**
     * Get createDate
     *
     * @return \DateTime 
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * Add posts
     *
     * @param Comment $comment
     * @return CommentGroup
     */
    public function addComment(Comment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove posts
     *
     * @param Comment $comment
     */
    public function removeComment(Comment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return CommentGroup
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
