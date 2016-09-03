<?php

namespace LaDanse\ServicesBundle\Service\DTO\GameData;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;

/**
 * @ExclusionPolicy("none")
 */
class Realm
{
    /**
     * @var string
     * @Type("string")
     * @SerializedName("id")
     */
    private $id;

    /**
     * @var string
     * @Type("string")
     * @SerializedName("name")
     */
    private $name;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return Realm
     */
    public function setId(string $id): Realm
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }
}