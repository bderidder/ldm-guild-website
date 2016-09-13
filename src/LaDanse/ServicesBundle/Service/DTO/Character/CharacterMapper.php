<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Service\DTO\Character;

use LaDanse\DomainBundle\Entity as Entity;
use LaDanse\ServicesBundle\Service\Character\Query\ClaimHydrator;
use LaDanse\ServicesBundle\Service\DTO\MapperException;
use LaDanse\ServicesBundle\Service\DTO\Reference\AccountReference;
use LaDanse\ServicesBundle\Service\DTO\Reference\StringReference;

class CharacterMapper
{
    /**
     * @param Entity\CharacterVersion $characterVersion
     * @param Entity\GameData\Guild $guild
     * @param ClaimHydrator $claimHydrator
     *
     * @return Character
     *
     * @internal param Entity\Claim $claim
     *
     */
    public static function mapSingle(
        Entity\CharacterVersion $characterVersion,
        $guild,
        ClaimHydrator $claimHydrator) : Character
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

        if ($claimHydrator->hasBeenClaimed($characterVersion->getCharacter()->getId()))
        {
            $claim = $claimHydrator->getClaim($characterVersion->getCharacter()->getId());

            $claimDto = new Claim();
            $claimDto
                ->setComment($claim->getComment())
                ->setAccountReference(
                    new AccountReference(
                        $claim->getAccount()->getId(),
                        $claim->getAccount()->getDisplayName()
                    )
                )
                ->setRaider($claim->isRaider())
                ->setRoles($claimHydrator->getClaimedRoles($characterVersion->getCharacter()->getId()));

            $dto->setClaim($claimDto);
        }

        return $dto;
    }

    /**
     * @param array $characterVersions
     * @param Entity\GameData\Guild $guild
     * @param ClaimHydrator $claimHydrator
     *
     * @return array
     *
     * @throws MapperException
     *
     */
    public static function mapArray(array $characterVersions, $guild, ClaimHydrator $claimHydrator) : array
    {
        $dtoArray = [];

        foreach($characterVersions as $characterVersion)
        {
            if (!($characterVersion instanceof Entity\CharacterVersion))
            {
                throw new MapperException('Element in array is not of type Entity\CharacterVersion');
            }

            /** @var Entity\CharacterVersion $characterVersion */
            $dtoArray[] = CharacterMapper::mapSingle(
                $characterVersion,
                $guild,
                $claimHydrator);
        }

        return $dtoArray;
    }
}