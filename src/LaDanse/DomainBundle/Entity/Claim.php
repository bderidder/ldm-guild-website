<?php

namespace LaDanse\DomainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="CharacterClaim")
 */
class Claim
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime", length=255, nullable=false)
     */
    protected $fromTime;

    /**
     * @ORM\Column(type="datetime", length=255, nullable=true)
     */
    protected $endTime;

    /**
     * @ORM\ManyToOne(targetEntity="Account")
     * @ORM\JoinColumn(name="accountId", referencedColumnName="id", nullable=false)
     */
    protected $account;

    /**
     * @ORM\ManyToOne(targetEntity="Character")
     * @ORM\JoinColumn(name="characterId", referencedColumnName="id", nullable=false)
     */
    protected $character;

    /**
     * @ORM\OneToMany(targetEntity="PlaysRole", mappedBy="claim", cascade={"persist", "remove"})
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
     * Set fromTime
     *
     * @param \DateTime $fromTime
     * @return Claim
     */
    public function setFromTime($fromTime)
    {
        $this->fromTime = $fromTime;

        return $this;
    }

    /**
     * Get fromTime
     *
     * @return \DateTime 
     */
    public function getFromTime()
    {
        return $this->fromTime;
    }

    /**
     * Set endTime
     *
     * @param \DateTime $endTime
     * @return Claim
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
     * Set account
     *
     * @param \LaDanse\DomainBundle\Entity\Account $account
     * @return Claim
     */
    public function setAccount(\LaDanse\DomainBundle\Entity\Account $account)
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
     * Set character
     *
     * @param \LaDanse\DomainBundle\Entity\Character $character
     * @return Claim
     */
    public function setCharacter(\LaDanse\DomainBundle\Entity\Character $character)
    {
        $this->character = $character;

        return $this;
    }

    /**
     * Get character
     *
     * @return \LaDanse\DomainBundle\Entity\Character 
     */
    public function getCharacter()
    {
        return $this->character;
    }

    /**
     * Add roles
     *
     * @param \LaDanse\DomainBundle\Entity\PlaysRole $roles
     * @return Claim
     */
    public function addRole(\LaDanse\DomainBundle\Entity\PlaysRole $roles)
    {
        $this->roles[] = $roles;

        return $this;
    }

    /**
     * Remove roles
     *
     * @param \LaDanse\DomainBundle\Entity\PlaysRole $roles
     */
    public function removeRole(\LaDanse\DomainBundle\Entity\PlaysRole $roles)
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
}
