<?php

namespace LaDanse\ServicesBundle\Service\DTO\Event;

use JMS\Serializer\Annotation\SerializedName;
use LaDanse\ServicesBundle\Service\DTO\Reference\AccountReference;
use LaDanse\ServicesBundle\Service\DTO\Reference\CommentGroupReference;

class Event
{
    /**
     * @SerializedName("id")
     *
     * @var int
     */
    protected $id;

    /**
     * @SerializedName("name")
     *
     * @var string
     */
    protected $name;

    /**
     * @SerializedName("description")
     *
     * @var string
     */
    protected $description;

    /**
     * @SerializedName("organiserRef")
     *
     * @var AccountReference
     */
    protected $organiser;

    /**
     * @SerializedName("inviteTime")
     *
     * @var \DateTime
     */
    protected $inviteTime;

    /**
     * @SerializedName("startTime")
     *
     * @var \DateTime
     */
    protected $startTime;

    /**
     * @SerializedName("endTime")
     *
     * @var \DateTime
     */
    protected $endTime;

    /**
     * @SerializedName("commentGroupRef")
     *
     * @var CommentGroupReference
     */
    protected $commentGroup;

    /**
     * @SerializedName("signUps")
     *
     * @var array
     */
    protected $signUps;

    /**
     * Event constructor.
     *
     * @param int $id
     * @param string $name
     * @param string $description
     * @param AccountReference $organiser
     * @param \DateTime $inviteTime
     * @param \DateTime $startTime
     * @param \DateTime $endTime
     * @param CommentGroupReference $commentGroup
     * @param array $signUps
     */
    public function __construct($id,
                                $name,
                                $description,
                                AccountReference $organiser,
                                \DateTime $inviteTime,
                                \DateTime $startTime,
                                \DateTime $endTime,
                                CommentGroupReference $commentGroup,
                                array $signUps)
    {
        $this->id = $id;
        $this->name = $name;
        $this->organiser = $organiser;
        $this->description = $description;
        $this->inviteTime = $inviteTime;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->commentGroup = $commentGroup;
        $this->signUps = $signUps;
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return AccountReference
     */
    public function getOrganiser()
    {
        return $this->organiser;
    }

    /**
     * @return \DateTime
     */
    public function getInviteTime()
    {
        return $this->inviteTime;
    }

    /**
     * @return \DateTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @return \DateTime
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @return array
     */
    public function getSignUps()
    {
        return $this->signUps;
    }

    /**
     * @return CommentGroupReference
     */
    public function getCommentGroup()
    {
        return $this->commentGroup;
    }

    /**
     * @param CommentGroupReference $commentGroup
     */
    public function setCommentGroup($commentGroup)
    {
        $this->commentGroup = $commentGroup;
    }
}