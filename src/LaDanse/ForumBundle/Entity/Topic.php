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
     * @var integer
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
     * @ORM\OneToMany(targetEntity="Post", mappedBy="topic", cascade={"persist", "remove"})
     */
    protected $posts;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->posts = new ArrayCollection();
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
    public function setCreator(\LaDanse\DomainBundle\Entity\Account $creator = null)
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
     * Set id
     *
     * @param integer $id
     * @return Topic
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
