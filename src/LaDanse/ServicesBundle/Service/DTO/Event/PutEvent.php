<?php

namespace LaDanse\ServicesBundle\Service\DTO\Event;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use LaDanse\ServicesBundle\Service\DTO\Reference\IntegerReference;
use Symfony\Component\Validator\Constraints as Assert;

class PutEvent
{
    /**
     * @var string
     * @Type("string")
     * @SerializedName("name")
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     * @Type("string")
     * @SerializedName("description")
     */
    private $description;

    /**
     * @var \DateTime
     * @Type("DateTime")
     * @SerializedName("inviteTime")
     * @Assert\NotNull()
     */
    private $inviteTime;

    /**
     * @var \DateTime
     *
     * @Type("DateTime")
     * @SerializedName("startTime")
     * @Assert\NotNull()
     */
    private $startTime;

    /**
     * @var \DateTime
     * @Type("DateTime")
     * @SerializedName("endTime")
     * @Assert\NotNull()
     */
    private $endTime;

    /**
     * @var IntegerReference
     * @Type(IntegerReference::class)
     * @SerializedName("organiserReference")
     * @Assert\NotNull()
     */
    private $organiserReference;

    public function __construct()
    {
        $this->description = "";
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
     * @return PutEvent
     */
    public function setName($name): PutEvent
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
     * @return PutEvent
     */
    public function setDescription($description): PutEvent
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getInviteTime(): \DateTime
    {
        return $this->inviteTime;
    }

    /**
     * @param \DateTime $inviteTime
     * @return PutEvent
     */
    public function setInviteTime(\DateTime $inviteTime): PutEvent
    {
        $this->inviteTime = $inviteTime;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStartTime(): \DateTime
    {
        return $this->startTime;
    }

    /**
     * @param \DateTime $startTime
     * @return PutEvent
     */
    public function setStartTime(\DateTime $startTime): PutEvent
    {
        $this->startTime = $startTime;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEndTime(): \DateTime
    {
        return $this->endTime;
    }

    /**
     * @param \DateTime $endTime
     * @return PutEvent
     */
    public function setEndTime(\DateTime $endTime): PutEvent
    {
        $this->endTime = $endTime;
        return $this;
    }

    /**
     * @return IntegerReference
     */
    public function getOrganiserReference(): IntegerReference
    {
        return $this->organiserReference;
    }

    /**
     * @param IntegerReference $organiserReference
     * @return PutEvent
     */
    public function setOrganiserReference(IntegerReference $organiserReference): PutEvent
    {
        $this->organiserReference = $organiserReference;
        return $this;
    }
}