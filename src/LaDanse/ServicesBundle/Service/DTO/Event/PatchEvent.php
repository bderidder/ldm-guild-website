<?php

namespace LaDanse\ServicesBundle\Service\DTO\Event;

use JMS\Serializer\Annotation\SerializedName;

class PatchEvent
{
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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
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
     */
    public function setDescription($description)
    {
        $this->description = $description;
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
     */
    public function setInviteTime($inviteTime)
    {
        $this->inviteTime = $inviteTime;
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
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
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
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;
    }
}