<?php

namespace LaDanse\ServicesBundle\Service\DTO\GameData;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;

/**
 * @ExclusionPolicy("none")
 */
class GameClass
{
    /**
     * @var string $id
     * @Type("string")
     * @SerializedName("id")
     */
    protected $id;

    /**
     * @var integer $armoryId
     * @Type("integer")
     * @SerializedName("armoryId")
     */
    protected $armoryId;

    /**
     * @var string $name
     * @Type("string")
     * @SerializedName("name")
     */
    protected $name;

    /**
     * @return string
     */
    public function getId() : string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return GameClass
     */
    public function setId(string $id) : GameClass
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getArmoryId()
    {
        return $this->armoryId;
    }

    /**
     * @param int $armoryId
     * @return GameClass
     */
    public function setArmoryId($armoryId) : GameClass
    {
        $this->armoryId = $armoryId;
        return $this;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return GameClass
     */
    public function setName(string $name) : GameClass
    {
        $this->name = $name;
        return $this;
    }
}