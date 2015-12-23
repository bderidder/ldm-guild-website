<?php

namespace LaDanse\ServicesBundle\Service\DTO\Forum;

use JMS\Serializer\Annotation\SerializedName;
use LaDanse\ServicesBundle\Service\DTO\Reference\AccountReference;

class TopicEntry
{
    /**
     * @SerializedName("id")
     *
     * @var string
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
     * @SerializedName("lastPost")
     *
     * @var LastPostEntry
     */
    protected $lastPost;

    public function __construct($id,
                                $subject,
                                \DateTime $createDate,
                                AccountReference $creatorRef,
                                LastPostEntry $lastPost)
    {
        $this->id = $id;
        $this->subject = $subject;
        $this->createDate = $createDate;
        $this->creatorRef = $creatorRef;
        $this->lastPost = $lastPost;
    }

    /**
     * @return string
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
     * @return LastPostEntry
     */
    public function getLastPost()
    {
        return $this->lastPost;
    }
}