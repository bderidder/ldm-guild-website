<?php

namespace LaDanse\ServicesBundle\Service\DTO\Character;

class PatchGuildCharacter
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $guild;

    /** @var int */
    protected $level;

    /** @var string */
    protected $gameClass;

    /** @var string */
    protected $gameRace;

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