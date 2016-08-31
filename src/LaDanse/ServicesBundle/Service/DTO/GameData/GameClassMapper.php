<?php

namespace LaDanse\ServicesBundle\Service\DTO\GameData;

use LaDanse\DomainBundle\Entity as Entity;
use LaDanse\ServicesBundle\Service\DTO\MapperException;
use LaDanse\ServicesBundle\Service\DTO\Reference\RealmReference;

class GameClassMapper
{
    /**
     * @param Entity\GameData\GameClass $gameClass
     * @return GameClass
     */
    public static function mapSingle(Entity\GameData\GameClass $gameClass) : GameClass
    {
        $dtoGameClass = new GameClass();

        $dtoGameClass->setId($gameClass->getId());
        $dtoGameClass->setArmoryId($gameClass->getId());
        $dtoGameClass->setName($gameClass->getName());

        return $dtoGameClass;
    }

    /**
     * @param array $gameClasss
     * @return array
     * @throws MapperException
     */
    public static function mapArray(array $gameClasss) : array
    {
        $dtoGameClassArray = [];

        foreach($gameClasss as $gameClass)
        {
            if (!($gameClass instanceof Entity\GameData\GameClass))
            {
                throw new MapperException('Element in array is not of type Entity\GameData\GameClass');
            }

            /** @var Entity\GameData\GameClass $gameClass */
            $dtoGameClassArray[] = GameClassMapper::mapSingle($gameClass);
        }

        return $dtoGameClassArray;
    }
}