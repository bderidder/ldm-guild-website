<?php

namespace LaDanse\DomainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="LoginEvent")
 */
class LoginEvent
{
    const REPOSITORY = 'LaDanseDomainBundle:LoginEvent';

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $loginTime;

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
     * Set loginTime
     *
     * @param \DateTime $loginTime
     * @return LoginEvent
     */
    public function setLoginTime($loginTime)
    {
        $this->loginTime = $loginTime;

        return $this;
    }

    /**
     * Get loginTime
     *
     * @return \DateTime 
     */
    public function getLoginTime()
    {
        return $this->loginTime;
    }

    /**
     * Set account
     *
     * @param Account $account
     * @return LoginEvent
     */
    public function setAccount(Account $account = null)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get account
     *
     * @return Account
     */
    public function getAccount()
    {
        return $this->account;
    }
}
