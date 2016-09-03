<?php

namespace LaDanse\ServicesBundle\Service\DTO\GameData;

use LaDanse\DomainBundle\Entity as Entity;
use LaDanse\ServicesBundle\Service\DTO\MapperException;
use LaDanse\ServicesBundle\Service\DTO\Reference\RealmReference;

class GuildMapper
{
    /**
     * @param Entity\GameData\Guild $guild
     * @return Guild
     */
    public static function mapSingle(Entity\GameData\Guild $guild) : Guild
    {
        $dtoGuild = new Guild();

        $dtoGuild->setId($guild->getId());
        $dtoGuild->setName($guild->getName());

        $dtoRealmReference = new RealmReference();
        $dtoRealmReference->setId($guild->getRealm()->getId());

        $dtoGuild->setRealmReference($dtoRealmReference);

        return $dtoGuild;
    }

    /**
     * @param array $guilds
     * @return array
     * @throws MapperException
     */
    public static function mapArray(array $guilds) : array
    {
        $dtoGuildArray = [];

        foreach($guilds as $guild)
        {
            if (!($guild instanceof Entity\GameData\Guild))
            {
                throw new MapperException('Element in array is not of type Entity\GameData\Guild');
            }

            /** @var Entity\GameData\Guild $guild */
            $dtoGuildArray[] = GuildMapper::mapSingle($guild);
        }

        return $dtoGuildArray;
    }
}