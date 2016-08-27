<?php

namespace LaDanse\ServicesBundle\Service\DTO\GameData;

class PatchGuild
{
    /** @var string */
    private $name;

    /** @var string */
    private $realmId;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return PatchGuild
     */
    public function setName(string $name): PatchGuild
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getRealmId(): string
    {
        return $this->realmId;
    }

    /**
     * @param string $realmId
     * @return PatchGuild
     */
    public function setRealmId(string $realmId): PatchGuild
    {
        $this->realmId = $realmId;
        return $this;
    }
}