<?php

namespace LaDanse\ForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\ArrayCollection;

use LaDanse\DomainBundle\Entity\Account;

/**
 * Topic
 *
 * @ORM\Table(name="Topic")
 * @ORM\Entity
 */
class Topic
{
    const REPOSITORY = 'LaDanseForumBundle:Topic';

    /**
     * @var string
     *
     * @ORM\Column(name="topicId", type="guid")
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
     * @ORM\ManyToOne(targetEntity="LaDanse\DomainBundle\Entity\Account")
     * @ORM\JoinColumn(name="posterId", referencedColumnName="id", nullable=false)
     */
    private $creator;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=255)
     */
    private $subject;

    /**
     * @ORM\ManyToOne(targetEntity="Forum", inversedBy="topics")
     * @ORM\JoinColumn(name="forumId", referencedColumnName="forumId", nullable=true)
     */
    private $forum;

    /**
     * @ORM\OneToMany(targetEntity="Post", mappedBy="topic", cascade={"persist", "remove"})
     */
    protected $posts;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastPostDate", type="datetime", nullable=true)
     */
    private $lastPostDate;

    /**
     * @ORM\ManyToOne(targetEntity="LaDanse\DomainBundle\Entity\Account")
     * @ORM\JoinColumn(name="lastPostPoster", referencedColumnName="id", nullable=true)
     */
    private $lastPostPoster;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->posts = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set id
     *
     * @param string $id
     * @return Topic
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     * @return Topic
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
     * Set subject
     *
     * @param string $subject
     * @return Topic
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string 
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set creator
     *
     * @param \LaDanse\DomainBundle\Entity\Account $creator
     * @return Topic
     */
    public function setCreator(\LaDanse\DomainBundle\Entity\Account $creator)
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * Get creator
     *
     * @return \LaDanse\DomainBundle\Entity\Account 
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * Set forum
     *
     * @param \LaDanse\ForumBundle\Entity\Forum $forum
     * @return Topic
     */
    public function setForum(\LaDanse\ForumBundle\Entity\Forum $forum = null)
    {
        $this->forum = $forum;

        return $this;
    }

    /**
     * Get forum
     *
     * @return \LaDanse\ForumBundle\Entity\Forum 
     */
    public function getForum()
    {
        return $this->forum;
    }

    /**
     * Add posts
     *
     * @param \LaDanse\ForumBundle\Entity\Post $posts
     * @return Topic
     */
    public function addPost(\LaDanse\ForumBundle\Entity\Post $posts)
    {
        $this->posts[] = $posts;

        return $this;
    }

    /**
     * Remove posts
     *
     * @param \LaDanse\ForumBundle\Entity\Post $posts
     */
    public function removePost(\LaDanse\ForumBundle\Entity\Post $posts)
    {
        $this->posts->removeElement($posts);
    }

    /**
     * Get posts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * @return \DateTime
     */
    public function getLastPostDate()
    {
        return $this->lastPostDate;
    }

    /**
     * @param \DateTime $lastPostDate
     * @return Forum
     */
    public function setLastPostDate($lastPostDate)
    {
        $this->lastPostDate = $lastPostDate;

        return $this;
    }

    /**
     * @return Account
     */
    public function getLastPostPoster()
    {
        return $this->lastPostPoster;
    }

    /**
     * @param Account $lastPostPoster
     * @return Forum
     */
    public function setLastPostPoster(Account $lastPostPoster)
    {
        $this->lastPostPoster = $lastPostPoster;

        return $this;
    }
}
