<?php

namespace LaDanse\ServicesBundle\Service\DTO\Reference;

class RealmReference
{
    /** @var string */
    private $realmId;

    /**
     * @return string
     */
    public function getRealmId(): string
    {
        return $this->realmId;
    }

    /**
     * @param string $realmId
     * @return RealmReference
     */
    public function setRealmId(string $realmId): RealmReference
    {
        $this->realmId = $realmId;
        return $this;
    }
}