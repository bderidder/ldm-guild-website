<?php

namespace LaDanse\ServicesBundle\Service\DTO\Event;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\SerializedName;
use LaDanse\ServicesBundle\Service\DTO\Reference\AccountReference;
use LaDanse\ServicesBundle\Service\DTO\Reference\CommentGroupReference;

/**
 * @ExclusionPolicy("none")
 */
class Event
{
    /**
     * @var int
     * @SerializedName("id")
     */
    protected $id;

    /**
     * @var string
     * @SerializedName("name")
     */
    protected $name;

    /**
     * @var string
     * @SerializedName("description")
     */
    protected $description;

    /**
     * @var AccountReference
     * @SerializedName("organiserRef")
     */
    protected $organiser;

    /**
     * @var \DateTime
     * @SerializedName("inviteTime")
     */
    protected $inviteTime;

    /**
     * @var \DateTime
     * @SerializedName("startTime")
     */
    protected $startTime;

    /**
     * @var \DateTime
     * @SerializedName("endTime")
     */
    protected $endTime;

    /**
     * @var string
     * @SerializedName("state")
     */
    protected $state;

    /**
     * @var CommentGroupReference
     * @SerializedName("commentGroupRef")
     */
    protected $commentGroup;

    /**
     * @var array
     * @SerializedName("signUps")
     */
    protected $signUps;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Event
     */
    public function setId(int $id): Event
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Event
     */
    public function setName($name): Event
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Event
     */
    public function setDescription($description): Event
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return AccountReference
     */
    public function getOrganiser(): AccountReference
    {
        return $this->organiser;
    }

    /**
     * @param AccountReference $organiser
     * @return Event
     */
    public function setOrganiser(AccountReference $organiser): Event
    {
        $this->organiser = $organiser;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getInviteTime()
    {
        return $this->inviteTime;
    }

    /**
     * @param \DateTime $inviteTime
     * @return Event
     */
    public function setInviteTime(\DateTime $inviteTime): Event
    {
        $this->inviteTime = $inviteTime;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @param \DateTime $startTime
     * @return Event
     */
    public function setStartTime(\DateTime $startTime): Event
    {
        $this->startTime = $startTime;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @param \DateTime $endTime
     * @return Event
     */
    public function setEndTime(\DateTime $endTime)
    {
        $this->endTime = $endTime;
        return $this;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     * @return Event
     */
    public function setState($state): Event
    {
        $this->state = $state;
        return $this;
    }

    /**
     * @return CommentGroupReference
     */
    public function getCommentGroup(): CommentGroupReference
    {
        return $this->commentGroup;
    }

    /**
     * @param CommentGroupReference $commentGroup
     * @return Event
     */
    public function setCommentGroup($commentGroup): Event
    {
        $this->commentGroup = $commentGroup;
        return $this;
    }

    /**
     * @return array
     */
    public function getSignUps(): array
    {
        return $this->signUps;
    }

    /**
     * @param array $signUps
     * @return Event
     */
    public function setSignUps($signUps): Event
    {
        $this->signUps = $signUps;
        return $this;
    }
}