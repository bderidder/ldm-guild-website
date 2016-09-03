<?php

namespace LaDanse\ServicesBundle\Service\DTO\GameData;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use LaDanse\ServicesBundle\Service\DTO\Reference\StringReference;
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
     * @var StringReference
     * @Type(StringReference::class)
     * @SerializedName("realmId")
     * @Assert\NotNull()
     * @Assert\Valid()
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
     * @return StringReference
     */
    public function getRealmId(): StringReference
    {
        return $this->realmId;
    }

    /**
     * @param StringReference $realmId
     * @return PatchGuild
     */
    public function setRealmId(StringReference $realmId): PatchGuild
    {
        $this->realmId = $realmId;
        return $this;
    }
}