<?php

namespace LaDanse\ServicesBundle\Service\DTO\Topic;

use JMS\Serializer\Annotation\SerializedName;
use LaDanse\ServicesBundle\Service\DTO\Reference\AccountReference;

class Topic
{
    /**
     * @SerializedName("id")
     *
     * @var int
     */
    protected $id;

    /**
     * @SerializedName("subject")
     *
     * @var string
     */
    protected $subject;

    /**
     * @SerializedName("createDate")
     *
     * @var \DateTime
     */
    protected $createDate;

    /**
     * @SerializedName("creatorRef")
     *
     * @var AccountReference
     */
    protected $creatorRef;

    /**
     * @SerializedName("posts")
     *
     * @var array
     */
    protected $posts;

    /**
     * Topic constructor.
     *
     * @param string $id
     * @param string $subject
     * @param \DateTime $createDate
     * @param AccountReference $creatorRef
     * @param array|null $posts
     */
    public function __construct($id,
                                $subject,
                                \DateTime $createDate,
                                AccountReference $creatorRef,
                                array $posts = null)
    {
        $this->id = $id;
        $this->subject = $subject;
        $this->createDate = $createDate;
        $this->creatorRef = $creatorRef;
        $this->posts = $posts;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return \DateTime
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * @return AccountReference
     */
    public function getCreatorRef()
    {
        return $this->creatorRef;
    }

    /**
     * @return array
     */
    public function getPosts()
    {
        return $this->posts;
    }
}