<?php
/**
 * Created by PhpStorm.
 * User: bavo
 * Date: 21/08/16
 * Time: 17:39
 */

namespace LaDanse\ServicesBundle\Service\DTO\GameData;


use LaDanse\ServicesBundle\Service\DTO\Reference\RealmReference;

class Guild
{
    /** @var string */
    private $id;

    /** @var string */
    private $name;

    /** @var RealmReference */
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