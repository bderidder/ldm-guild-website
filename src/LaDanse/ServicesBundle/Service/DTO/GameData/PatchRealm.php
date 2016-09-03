<?php

namespace LaDanse\ServicesBundle\Service\DTO\GameData;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ExclusionPolicy("none")
 */
class PatchRealm
{
    /**
     * @var string
     * @Type("string")
     * @SerializedName("name")
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return PatchRealm
     */
    public function setName(string $name): PatchRealm
    {
        $this->name = $name;
        return $this;
    }
}