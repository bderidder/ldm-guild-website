<?php

namespace LaDanse\ForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\ArrayCollection;

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
     * @var integer
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
     * @ORM\ManyToMany(targetEntity="Topic")
     * @ORM\JoinTable(name="TopicsInForum",
     *      joinColumns={@ORM\JoinColumn(name="forumId", referencedColumnName="forumId")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="topicId", referencedColumnName="topicId")}
     *      )
     **/
    protected $topics;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->topics = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return guid 
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

    /**
     * Set id
     *
     * @param guid $id
     * @return Forum
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
