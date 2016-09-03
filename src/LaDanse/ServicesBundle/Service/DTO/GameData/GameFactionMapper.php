<?php

namespace LaDanse\ServicesBundle\Service\DTO\GameData;

use LaDanse\DomainBundle\Entity as Entity;
use LaDanse\ServicesBundle\Service\DTO\MapperException;

class GameFactionMapper
{
    /**
     * @param Entity\GameData\GameFaction $gameFaction
     * @return GameFaction
     */
    public static function mapSingle(Entity\GameData\GameFaction $gameFaction) : GameFaction
    {
        $dtoGameFaction = new GameFaction();

        $dtoGameFaction->setId($gameFaction->getId());
        $dtoGameFaction->setArmoryId($gameFaction->getId());
        $dtoGameFaction->setName($gameFaction->getName());

        return $dtoGameFaction;
    }

    /**
     * @param array $gameFactions
     * @return array
     * @throws MapperException
     */
    public static function mapArray(array $gameFactions) : array
    {
        $dtoGameFactionArray = [];

        foreach($gameFactions as $gameFaction)
        {
            if (!($gameFaction instanceof Entity\GameData\GameFaction))
            {
                throw new MapperException('Element in array is not of type Entity\GameData\GameFaction');
            }

            /** @var Entity\GameData\GameFaction $gameFaction */
            $dtoGameFactionArray[] = GameFactionMapper::mapSingle($gameFaction);
        }

        return $dtoGameFactionArray;
    }
}