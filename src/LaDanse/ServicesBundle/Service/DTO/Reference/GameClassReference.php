<?php

namespace LaDanse\ServicesBundle\Service\DTO\Reference;

class GameClassReference
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
     * @return GameClassReference
     */
    public function setId(int $id): GameClassReference
    {
        $this->id = $id;
        return $this;
    }
}