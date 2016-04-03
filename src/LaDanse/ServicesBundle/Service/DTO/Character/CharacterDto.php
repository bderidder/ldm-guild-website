<?php

namespace LaDanse\ServicesBundle\Service\DTO\Character;

class CharacterDto
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $name;

    /** @var string */
    protected $guild;

    /** @var string */
    protected $realm;

    /** @var int */
    protected $level;

    /** @var string */
    protected $gameClass;

    /** @var string */
    protected $gameRace;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getGuild()
    {
        return $this->guild;
    }

    /**
     * @param string $guild
     */
    public function setGuild($guild)
    {
        $this->guild = $guild;
    }

    /**
     * @return string
     */
    public function getRealm()
    {
        return $this->realm;
    }

    /**
     * @param string $realm
     */
    public function setRealm($realm)
    {
        $this->realm = $realm;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param int $level
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * @return string
     */
    public function getGameClass()
    {
        return $this->gameClass;
    }

    /**
     * @param string $gameClass
     */
    public function setGameClass($gameClass)
    {
        $this->gameClass = $gameClass;
    }

    /**
     * @return string
     */
    public function getGameRace()
    {
        return $this->gameRace;
    }

    /**
     * @param string $gameRace
     */
    public function setGameRace($gameRace)
    {
        $this->gameRace = $gameRace;
    }
}