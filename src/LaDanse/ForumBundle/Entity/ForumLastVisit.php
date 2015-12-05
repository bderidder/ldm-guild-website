<?php

namespace LaDanse\ForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use LaDanse\DomainBundle\Entity\Account;

/**
 * Topic
 *
 * @ORM\Table(name="ForumLastVisit")
 * @ORM\Entity
 */
class ForumLastVisit
{
    const REPOSITORY = 'LaDanseForumBundle:ForumLastVisit';

    /**
     * @var integer
     *
     * @ORM\Column(name="visitId", type="guid")
     * @ORM\Id
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastVisitDate", type="datetime")
     */
    private $lastVisitDate;

    /**
     * @ORM\ManyToOne(targetEntity="LaDanse\DomainBundle\Entity\Account")
     * @ORM\JoinColumn(name="accountId", referencedColumnName="id", nullable=false)
     */
    private $account;

    /**
     * Set id
     *
     * @param string $id
     * @return ForumLastVisit
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
     * Set lastVisitDate
     *
     * @param \DateTime $lastVisitDate
     * @return ForumLastVisit
     */
    public function setLastVisitDate($lastVisitDate)
    {
        $this->lastVisitDate = $lastVisitDate;

        return $this;
    }

    /**
     * Get lastVisitDate
     *
     * @return \DateTime 
     */
    public function getLastVisitDate()
    {
        return $this->lastVisitDate;
    }

    /**
     * Set account
     *
     * @param Account $account
     * @return ForumLastVisit
     */
    public function setAccount(Account $account)
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
