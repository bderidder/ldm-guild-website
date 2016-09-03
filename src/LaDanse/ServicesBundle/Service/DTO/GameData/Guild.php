<?php

namespace LaDanse\ServicesBundle\Service\DTO\GameData;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use LaDanse\ServicesBundle\Service\DTO\Reference\RealmReference;

/**
 * @ExclusionPolicy("none")
 */
class Guild
{
    /**
     * @var string
     * @Type("string")
     * @SerializedName("id")
     */
    private $id;

    /**
     * @var string
     * @Type("string")
     * @SerializedName("name")
     */
    private $name;

    /**
     * @var RealmReference
     * @Type(RealmReference::class)
     * @SerializedName("realmReference")
     */
    private $realmReference;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return Guild
     */
    public function setId(string $id): Guild
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
     * @return Guild
     */
    public function setName(string $name): Guild
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return RealmReference
     */
    public function getRealmReference(): RealmReference
    {
        return $this->realmReference;
    }

    /**
     * @param RealmReference $realmReference
     * @return Guild
     */
    public function setRealmReference(RealmReference $realmReference): Guild
    {
        $this->realmReference = $realmReference;
        return $this;
    }
}