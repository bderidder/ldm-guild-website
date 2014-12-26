<?php

namespace LaDanse\ForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\ArrayCollection;

use LaDanse\DomainBundle\Entity\Account;

/**
 * UnreadPost
 *
 * @ORM\Table(name="UnreadPost")
 * @ORM\Entity
 */
class UnreadPost
{
    const REPOSITORY = 'LaDanseForumBundle:UnreadPost';

    /**
     * @var integer
     *
     * @ORM\Column(name="unreadId", type="guid")
     * @ORM\Id
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="LaDanse\DomainBundle\Entity\Account")
     * @ORM\JoinColumn(name="accountId", referencedColumnName="id", nullable=false)
     */
    private $account;

    /**
     * @ORM\ManyToOne(targetEntity="LaDanse\ForumBundle\Entity\Post")
     * @ORM\JoinColumn(name="postId", referencedColumnName="postId", nullable=false)
     */
    private $post;
    

    /**
     * Set id
     *
     * @param string $id
     * @return UnreadPost
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
     * Set account
     *
     * @param \LaDanse\DomainBundle\Entity\Account $account
     * @return UnreadPost
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
     * Set post
     *
     * @param \LaDanse\ForumBundle\Entity\Post $post
     * @return UnreadPost
     */
    public function setPost(\LaDanse\ForumBundle\Entity\Post $post)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * Get post
     *
     * @return \LaDanse\ForumBundle\Entity\Post 
     */
    public function getPost()
    {
        return $this->post;
    }
}
