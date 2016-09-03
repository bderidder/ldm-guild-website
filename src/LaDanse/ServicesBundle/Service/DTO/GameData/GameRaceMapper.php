<?php

namespace LaDanse\ServicesBundle\Service\DTO\GameData;

use LaDanse\DomainBundle\Entity as Entity;
use LaDanse\ServicesBundle\Service\DTO\MapperException;
use LaDanse\ServicesBundle\Service\DTO\Reference\GameFactionReference;
use LaDanse\ServicesBundle\Service\DTO\Reference\RealmReference;

class GameRaceMapper
{
    /**
     * @param Entity\GameData\GameRace $gameRace
     * @return GameRace
     */
    public static function mapSingle(Entity\GameData\GameRace $gameRace) : GameRace
    {
        $dtoGameRace = new GameRace();

        $dtoGameRace->setId($gameRace->getId());
        $dtoGameRace->setArmoryId($gameRace->getId());
        $dtoGameRace->setName($gameRace->getName());

        $gameFactionReference = new GameFactionReference();
        $gameFactionReference->setId($gameRace->getFaction()->getId());

        $dtoGameRace->setGameFactionReference($gameFactionReference);

        return $dtoGameRace;
    }

    /**
     * @param array $gameRaces
     * @return array
     * @throws MapperException
     */
    public static function mapArray(array $gameRaces) : array
    {
        $dtoGameRaceArray = [];

        foreach($gameRaces as $gameRace)
        {
            if (!($gameRace instanceof Entity\GameData\GameRace))
            {
                throw new MapperException('Element in array is not of type Entity\GameData\GameRace');
            }

            /** @var Entity\GameData\GameRace $gameRace */
            $dtoGameRaceArray[] = GameRaceMapper::mapSingle($gameRace);
        }

        return $dtoGameRaceArray;
    }
}