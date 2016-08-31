<?php

namespace LaDanse\ServicesBundle\Service\DTO\GameData;

use LaDanse\DomainBundle\Entity as Entity;
use LaDanse\ServicesBundle\Service\DTO\MapperException;
use LaDanse\ServicesBundle\Service\DTO\Reference\RealmReference;

class RealmMapper
{
    /**
     * @param Entity\GameData\Realm $realm
     * @return Realm
     */
    public static function mapSingle(Entity\GameData\Realm $realm) : Realm
    {
        $dtoRealm = new Realm();

        $dtoRealm->setId($realm->getId());
        $dtoRealm->setName($realm->getName());

        return $dtoRealm;
    }

    /**
     * @param array $realms
     * @return array
     * @throws MapperException
     */
    public static function mapArray(array $realms) : array
    {
        $dtoRealmArray = [];

        foreach($realms as $realm)
        {
            if (!($realm instanceof Entity\GameData\Realm))
            {
                throw new MapperException('Element in array is not of type Entity\GameData\Realm');
            }

            /** @var Entity\GameData\Realm $realm */
            $dtoRealmArray[] = RealmMapper::mapSingle($realm);
        }

        return $dtoRealmArray;
    }
}