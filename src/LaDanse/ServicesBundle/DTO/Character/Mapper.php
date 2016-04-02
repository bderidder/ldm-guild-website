<?php

namespace LaDanse\ServicesBundle\DTO\Character;

use LaDanse\DomainBundle\Entity\Character;
use LaDanse\DomainBundle\Entity\CharacterVersion;

class Mapper
{
    /**
     * @param Character $character
     * @param CharacterVersion $characterVersion
     *
     * @return CharacterDto
     */
    public function mapFromEntities(Character $character, CharacterVersion $characterVersion)
    {
        $characterDto = new CharacterDto();

        $characterDto->setId($character->getId());
        $characterDto->setName($character->getName());
        $characterDto->setLevel($characterVersion->getLevel());
        $characterDto->setRealm($character->getRealm());
        $characterDto->setGuild($characterVersion->getGuild());
        $characterDto->setGameClass($characterVersion->getGameClass());
        $characterDto->setGameRace($characterVersion->getGameRace());

        return $characterDto;
    }

    /**
     * @param CharacterDto $characterDto
     *
     * @return array
     */
    public function mapToEntities(CharacterDto $characterDto)
    {
        return null;
    }
}