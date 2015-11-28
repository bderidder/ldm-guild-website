<?php

namespace LaDanse\ForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\ArrayCollection;
use LaDanse\DomainBundle\Entity\Account;

/**
 * Forum
 *
 * @ORM\Table(name="Forum")
 * @ORM\Entity
 */
class Forum
{
    const REPOSITORY = 'LaDanseForumBundle:Forum';

    /**
     * @var string
     *
     * @ORM\Column(name="forumId", type="guid")
     * @ORM\Id
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastPostDate", type="datetime", nullable=true)
     */
    private $lastPostDate;

    /**
     * @ORM\ManyToOne(targetEntity="Topic")
     * @ORM\JoinColumn(name="lastPostTopic", referencedColumnName="topicId", nullable=true)
     */
    private $lastPostTopic;

    /**
     * @ORM\ManyToOne(targetEntity="LaDanse\DomainBundle\Entity\Account")
     * @ORM\JoinColumn(name="lastPostPoster", referencedColumnName="id", nullable=true)
     */
    private $lastPostPoster;

    /**
     * @ORM\OneToMany(targetEntity="Topic", mappedBy="forum", cascade={"persist", "remove"})
     */
    protected $topics;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->topics = new ArrayCollection();
    }

    /**
     * Set id
     *
     * @param string $id
     * @return Forum
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
     * Set name
     *
     * @param string $name
     * @return Forum
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Forum
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
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
     * @return Topic
     */
    public function getLastPostTopic()
    {
        return $this->lastPostTopic;
    }

    /**
     * @param Topic $lastPostTopic
     * @return Forum
     */
    public function setLastPostTopic(Topic $lastPostTopic)
    {
        $this->lastPostTopic = $lastPostTopic;

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

    /**
     * Add topics
     *
     * @param \LaDanse\ForumBundle\Entity\Topic $topics
     * @return Forum
     */
    public function addTopic(\LaDanse\ForumBundle\Entity\Topic $topics)
    {
        $this->topics[] = $topics;

        return $this;
    }

    /**
     * Remove topics
     *
     * @param \LaDanse\ForumBundle\Entity\Topic $topics
     */
    public function removeTopic(\LaDanse\ForumBundle\Entity\Topic $topics)
    {
        $this->topics->removeElement($topics);
    }

    /**
     * Get topics
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTopics()
    {
        return $this->topics;
    }
}
