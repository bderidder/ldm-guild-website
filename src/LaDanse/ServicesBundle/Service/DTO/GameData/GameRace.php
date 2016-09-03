<?php

namespace LaDanse\ServicesBundle\Service\DTO\GameData;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use LaDanse\ServicesBundle\Service\DTO\Reference\StringReference;

/**
 * @ExclusionPolicy("none")
 */
class GameRace
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
     * @var StringReference
     * @Type(StringReference::class)
     * @SerializedName("gameFactionReference")
     */
    protected $gameFactionReference;

    /**
     * @return string
     */
    public function getId() : string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return GameRace
     */
    public function setId(string $id) : GameRace
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
     * @return GameRace
     */
    public function setArmoryId($armoryId) : GameRace
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
     * @return GameRace
     */
    public function setName(string $name) : GameRace
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return StringReference
     */
    public function getGameFactionReference(): StringReference
    {
        return $this->gameFactionReference;
    }

    /**
     * @param StringReference $gameFactionReference
     * @return GameRace
     */
    public function setGameFactionReference(StringReference $gameFactionReference): GameRace
    {
        $this->gameFactionReference = $gameFactionReference;
        return $this;
    }
}