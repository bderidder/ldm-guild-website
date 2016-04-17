<?php

namespace LaDanse\DomainBundle\Entity\Forum;

use Doctrine\ORM\Mapping as ORM;

use LaDanse\DomainBundle\Entity\Account;
use LaDanse\DomainBundle\Entity\Forum\Post;

/**
 * UnreadPost
 *
 * @ORM\Table(name="UnreadPost")
 * @ORM\Entity
 */
class UnreadPost
{
    const REPOSITORY = 'LaDanseDomainBundle:Forum\UnreadPost';

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
     * @ORM\ManyToOne(targetEntity="Post")
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
     * @param Account $account
     * @return UnreadPost
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

    /**
     * Set post
     *
     * @param Post $post
     * @return UnreadPost
     */
    public function setPost(Post $post)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * Get post
     *
     * @return Post
     */
    public function getPost()
    {
        return $this->post;
    }
}
