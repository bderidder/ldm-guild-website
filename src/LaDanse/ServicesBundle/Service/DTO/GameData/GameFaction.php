<?php

namespace LaDanse\ServicesBundle\Service\DTO\GameData;

class GameFaction
{
    /**
     * @var string $id
     */
    protected $id;

    /**
     * @var integer $armoryId
     */
    protected $armoryId;

    /**
     * @var string $name
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
     * @return GameFaction
     */
    public function setId(string $id) : GameFaction
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
     * @return GameFaction
     */
    public function setArmoryId($armoryId) : GameFaction
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
     * @return GameFaction
     */
    public function setName(string $name) : GameFaction
    {
        $this->name = $name;
        return $this;
    }
}