<?php

namespace LaDanse\ServicesBundle\Service\DTO\Reference;

class GameRaceReference
{
    /** @var int */
    protected $id;

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
}