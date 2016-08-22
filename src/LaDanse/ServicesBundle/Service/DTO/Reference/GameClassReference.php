<?php

namespace LaDanse\ServicesBundle\Service\DTO\Reference;

class GameClassReference
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
     * @return GameClassReference
     */
    public function setId(int $id): GameClassReference
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
     * @return GameClassReference
     */
    public function setName(string $name): GameClassReference
    {
        $this->name = $name;
        return $this;
    }
}