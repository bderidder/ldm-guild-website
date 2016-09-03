<?php

namespace LaDanse\ServicesBundle\Service\DTO\Reference;

class GameFactionReference
{
    /** @var string */
    protected $id;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return GameFactionReference
     */
    public function setId(string $id): GameFactionReference
    {
        $this->id = $id;
        return $this;
    }
}