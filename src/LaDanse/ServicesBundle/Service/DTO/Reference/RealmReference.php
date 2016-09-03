<?php

namespace LaDanse\ServicesBundle\Service\DTO\Reference;

class RealmReference
{
    /** @var string */
    private $id;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $realmId
     * @return RealmReference
     */
    public function setId(string $realmId): RealmReference
    {
        $this->id = $realmId;
        return $this;
    }
}