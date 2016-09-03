<?php

namespace LaDanse\ServicesBundle\Service\DTO\GameData;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ExclusionPolicy("none")
 */
class PatchGuild
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
     * @SerializedName("realmId")
     * @Assert\NotBlank()
     */
    private $realmId;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return PatchGuild
     */
    public function setName(string $name): PatchGuild
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getRealmId(): string
    {
        return $this->realmId;
    }

    /**
     * @param string $realmId
     * @return PatchGuild
     */
    public function setRealmId(string $realmId): PatchGuild
    {
        $this->realmId = $realmId;
        return $this;
    }
}