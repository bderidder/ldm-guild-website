<?php

namespace LaDanse\ForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\ArrayCollection;

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
     * @param guid $id
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
     * @return guid 
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
     * @param \LaDanse\DomainBundle\Entity\Account $account
     * @return ForumLastVisit
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
}
