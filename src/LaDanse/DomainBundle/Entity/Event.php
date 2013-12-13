<?php

namespace LaDanse\DomainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="Event")
 * @ORM\HasLifecycleCallbacks
 */
class Event
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    protected $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $inviteTime;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $startTime;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $endTime;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $lastModifiedTime;

    /**
     * @ORM\OneToMany(targetEntity="SignUp", mappedBy="event")
     */
    protected $signUps;

    /**
     * @ORM\ManyToOne(targetEntity="Account")
     * @ORM\JoinColumn(name="organiserId", referencedColumnName="id", nullable=false)
     */
    protected $organiser;

    public function __construct()
    {
        $this->signUps = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     */
    public function doPrePersist()
    {
        $this->lastModifiedTime = new \DateTime('now');
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
     * Set name
     *
     * @param string $name
     * @return Event
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
     * @return Event
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
     * Set inviteTime
     *
     * @param \DateTime $inviteTime
     * @return Event
     */
    public function setInviteTime($inviteTime)
    {
        $this->inviteTime = $inviteTime;

        return $this;
    }

    /**
     * Get inviteTime
     *
     * @return \DateTime 
     */
    public function getInviteTime()
    {
        return $this->inviteTime;
    }

    /**
     * Set startTime
     *
     * @param \DateTime $startTime
     * @return Event
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Get startTime
     *
     * @return \DateTime 
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Add signUps
     *
     * @param \LaDanse\DomainBundle\Entity\SignUp $signUps
     * @return Event
     */
    public function addSignUp(\LaDanse\DomainBundle\Entity\SignUp $signUps)
    {
        $this->signUps[] = $signUps;

        return $this;
    }

    /**
     * Remove signUps
     *
     * @param \LaDanse\DomainBundle\Entity\SignUp $signUps
     */
    public function removeSignUp(\LaDanse\DomainBundle\Entity\SignUp $signUps)
    {
        $this->signUps->removeElement($signUps);
    }

    /**
     * Get signUps
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSignUps()
    {
        return $this->signUps;
    }

    /**
     * Set organiser
     *
     * @param \LaDanse\DomainBundle\Entity\Account $organiser
     * @return Event
     */
    public function setOrganiser(\LaDanse\DomainBundle\Entity\Account $organiser = null)
    {
        $this->organiser = $organiser;

        return $this;
    }

    /**
     * Get organiser
     *
     * @return \LaDanse\DomainBundle\Entity\Account 
     */
    public function getOrganiser()
    {
        return $this->organiser;
    }

    /**
     * Set endTime
     *
     * @param \DateTime $endTime
     * @return Event
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * Get endTime
     *
     * @return \DateTime 
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Set lastModifiedTime
     *
     * @param \DateTime $lastModifiedTime
     * @return Event
     */
    public function setLastModifiedTime($lastModifiedTime)
    {
        $this->lastModifiedTime = $lastModifiedTime;

        return $this;
    }

    /**
     * Get lastModifiedTime
     *
     * @return \DateTime 
     */
    public function getLastModifiedTime()
    {
        return $this->lastModifiedTime;
    }
}
