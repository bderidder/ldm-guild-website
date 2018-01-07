<?php

namespace LaDanse\ServicesBundle\Service\DTO\Profile;


use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\SerializedName;

/**
 * @ExclusionPolicy("none")
 */
class Profile
{
    /**
     * @var int
     * @SerializedName("id")
     */
    private $id;

    /**
     * @var string
     * @SerializedName("displayName")
     */
    private $displayName;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * @param string $displayName
     */
    public function setDisplayName(string $displayName)
    {
        $this->displayName = $displayName;
    }
}