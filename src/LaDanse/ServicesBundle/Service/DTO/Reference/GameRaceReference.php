<?php

namespace LaDanse\ServicesBundle\Service\DTO\Reference;

class GameRaceReference
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $name;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return GameRaceReference
     */
    public function setId(int $id): GameRaceReference
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
     * @return GameRaceReference
     */
    public function setName(string $name): GameRaceReference
    {
        $this->name = $name;
        return $this;
    }
}