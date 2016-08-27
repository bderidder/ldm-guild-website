<?php

namespace LaDanse\ServicesBundle\Service\DTO\GameData;

class PatchRealm
{
    /** @var string */
    private $name;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return PatchRealm
     */
    public function setName(string $name): PatchRealm
    {
        $this->name = $name;
        return $this;
    }
}