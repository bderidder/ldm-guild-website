<?php

namespace LaDanse\DomainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\ManyToOne(targetEntity="Event")
     * @ORM\JoinColumn(name="eventId", referencedColumnName="id", nullable=false)
     */
    protected $event;

    /**
     * @ORM\ManyToOne(targetEntity="Account")
     * @ORM\JoinColumn(name="accountId", referencedColumnName="id", nullable=false)
     */
    protected $account;

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
}
