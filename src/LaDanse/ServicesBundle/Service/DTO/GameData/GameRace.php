<?php

namespace LaDanse\ServicesBundle\Service\DTO\GameData;

use LaDanse\ServicesBundle\Service\DTO\Reference\GameFactionReference;

class GameRace
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
     * @var GameFactionReference
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
     * @return GameFactionReference
     */
    public function getGameFactionReference(): GameFactionReference
    {
        return $this->gameFactionReference;
    }

    /**
     * @param GameFactionReference $gameFactionReference
     * @return GameRace
     */
    public function setGameFactionReference(GameFactionReference $gameFactionReference): GameRace
    {
        $this->gameFactionReference = $gameFactionReference;
        return $this;
    }
}