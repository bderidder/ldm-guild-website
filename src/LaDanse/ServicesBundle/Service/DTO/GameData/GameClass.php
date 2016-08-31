<?php

namespace LaDanse\ServicesBundle\Service\DTO\GameData;

class GameClass
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