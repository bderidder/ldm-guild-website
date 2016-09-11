<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Service\DTO\Character;

use LaDanse\DomainBundle\Entity as Entity;
use LaDanse\ServicesBundle\Service\DTO\MapperException;
use LaDanse\ServicesBundle\Service\DTO\Reference\StringReference;

class CharacterMapper
{
    /**
     * @param Entity\CharacterVersion $characterVersion
     * @param Entity\GameData\Guild $guild
     * @return Character
     */
    public static function mapSingle(Entity\CharacterVersion $characterVersion, $guild) : Character
    {
        $dto = new Character();

        $dto->setId($characterVersion->getCharacter()->getId());
        $dto->setName($characterVersion->getCharacter()->getName());
        $dto->setLevel($characterVersion->getLevel());

        $dto->setRealmReference(
            new StringReference($characterVersion->getCharacter()->getRealm()->getId())
        );

        $dto->setGameRaceReference(
            new StringReference($characterVersion->getGameRace()->getId())
        );

        $dto->setGameClassReference(
            new StringReference($characterVersion->getGameClass()->getId())
        );

        if ($guild != null)
        {
            $dto->setGuildReference(
                new StringReference($guild->getId())
            );
        }

        return $dto;
    }

    /**
     * @param array $characterVersions
     * @param Entity\GameData\Guild $guild
     * @return array
     * @throws MapperException
     */
    public static function mapArray(array $characterVersions, $guild) : array
    {
        $dtoArray = [];

        foreach($characterVersions as $characterVersion)
        {
            if (!($characterVersion instanceof Entity\CharacterVersion))
            {
                throw new MapperException('Element in array is not of type Entity\CharacterVersion');
            }

            /** @var Entity\CharacterVersion $characterVersion */
            $dtoArray[] = CharacterMapper::mapSingle($characterVersion, $guild);
        }

        return $dtoArray;
    }
}