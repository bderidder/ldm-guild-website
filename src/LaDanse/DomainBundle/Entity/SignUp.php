<?php

namespace LaDanse\DomainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\ArrayCollection;

use LaDanse\DomainBundle\Entity\Event;
use LaDanse\DomainBundle\Entity\Account;

/**
 * @ORM\Entity
 * @ORM\Table(name="SignUp")
 */
class SignUp
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="signUps")
     * @ORM\JoinColumn(name="eventId", referencedColumnName="id", nullable=false)
     */
    protected $event;

    /**
     * @ORM\ManyToOne(targetEntity="Account")
     * @ORM\JoinColumn(name="accountId", referencedColumnName="id", nullable=false)
     */
    protected $account;

    /**
     * @ORM\Column(type="string", length=15, nullable=false)
     */
    protected $type;

    /**
     * @ORM\OneToMany(targetEntity="ForRole", mappedBy="signUp", cascade={"persist", "remove"})
     */
    protected $roles;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
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
     * Set event
     *
     * @param \LaDanse\DomainBundle\Entity\Event $event
     * @return SignUp
     */
    public function setEvent(\LaDanse\DomainBundle\Entity\Event $event = null)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return \LaDanse\DomainBundle\Entity\Event 
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set account
     *
     * @param \LaDanse\DomainBundle\Entity\Account $account
     * @return SignUp
     */
    public function setAccount(\LaDanse\DomainBundle\Entity\Account $account = null)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get account
     *
     * @return \LaDanse\DomainBundle\Entity\Account 
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Add roles
     *
     * @param \LaDanse\DomainBundle\Entity\ForRole $roles
     * @return SignUp
     */
    public function addRole(\LaDanse\DomainBundle\Entity\ForRole $roles)
    {
        $this->roles[] = $roles;

        return $this;
    }

    /**
     * Remove roles
     *
     * @param \LaDanse\DomainBundle\Entity\ForRole $roles
     */
    public function removeRole(\LaDanse\DomainBundle\Entity\ForRole $roles)
    {
        $this->roles->removeElement($roles);
    }

    /**
     * Get roles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return SignUp
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }
}
