<?php

namespace LaDanse\DomainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="PlaysRole")
 */
class PlaysRole
{
    const REPOSITORY = 'LaDanseDomainBundle:PlaysRole';

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
     * @ORM\Column(type="string", length=15, nullable=false)
     */
    protected $role;

    /**
     * @ORM\ManyToOne(targetEntity="Claim", inversedBy="roles")
     * @ORM\JoinColumn(name="claimId", referencedColumnName="id", nullable=false)
     */
    protected $claim;

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
     * Set role
     *
     * @param string $role
     * @return PlaysRole
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string 
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set claim
     *
     * @param \LaDanse\DomainBundle\Entity\Claim $claim
     * @return PlaysRole
     */
    public function setClaim(\LaDanse\DomainBundle\Entity\Claim $claim)
    {
        $this->claim = $claim;

        return $this;
    }

    /**
     * Get claim
     *
     * @return \LaDanse\DomainBundle\Entity\Claim 
     */
    public function getClaim()
    {
        return $this->claim;
    }

    /**
     * Set fromTime
     *
     * @param \DateTime $fromTime
     * @return PlaysRole
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
     * @return PlaysRole
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
}
