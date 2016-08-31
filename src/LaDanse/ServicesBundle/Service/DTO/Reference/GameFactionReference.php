<?php

namespace LaDanse\ServicesBundle\Service\DTO\Reference;

class GameFactionReference
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
     * @return GameFactionReference
     */
    public function setId(int $id): GameFactionReference
    {
        $this->id = $id;
        return $this;
    }
}