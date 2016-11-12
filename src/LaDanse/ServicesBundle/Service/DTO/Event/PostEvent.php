<?php

namespace LaDanse\ServicesBundle\Service\DTO\Event;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use LaDanse\ServicesBundle\Service\DTO\Reference\IntegerReference;
use Symfony\Component\Validator\Constraints as Assert;

class PostEvent
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

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return PostEvent
     */
    public function setName($name): PostEvent
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
     * @return PostEvent
     */
    public function setDescription($description): PostEvent
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
     * @return PostEvent
     */
    public function setInviteTime(\DateTime $inviteTime): PostEvent
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
     * @return PostEvent
     */
    public function setStartTime(\DateTime $startTime): PostEvent
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
     * @return PostEvent
     */
    public function setEndTime(\DateTime $endTime): PostEvent
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
     * @return PostEvent
     */
    public function setOrganiserReference(IntegerReference $organiserReference): PostEvent
    {
        $this->organiserReference = $organiserReference;
        return $this;
    }
}