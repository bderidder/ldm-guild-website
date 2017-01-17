<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Command;

use LaDanse\ServicesBundle\Common\CommandExecutionContext;
use LaDanse\ServicesBundle\Service\DTO\Character\Character;
use LaDanse\ServicesBundle\Service\DTO\Character\PatchCharacter;
use LaDanse\ServicesBundle\Service\DTO\GameData\GameClass;
use LaDanse\ServicesBundle\Service\DTO\GameData\GameRace;
use LaDanse\ServicesBundle\Service\DTO\GameData\Guild;
use LaDanse\ServicesBundle\Service\DTO\GameData\PatchGuild;
use LaDanse\ServicesBundle\Service\DTO\GameData\PatchRealm;
use LaDanse\ServicesBundle\Service\DTO\GameData\Realm;
use LaDanse\ServicesBundle\Service\DTO\Reference\StringReference;
use LaDanse\ServicesBundle\Service\GameData\GameDataService;
use LaDanse\ServicesBundle\Service\Character\CharacterService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RefreshGuildMembersCommand
 * @package LaDanse\ServicesBundle\Command
 */
class RefreshGuildMembersCommand extends ContainerAwareCommand
{
    const BATTLENET_API_URL =
        "https://eu.api.battle.net/wow/guild/Defias%20Brotherhood/La%20Danse%20Macabre?fields=members&locale=en_GB&apikey=";

    private $gameRaces;
    private $gameClasses;
    private $guilds;
    private $realms;

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('ladanse:refreshGuildMembers')
            ->setDescription('Refresh guild members from the armory')
        ;
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $context = new CommandExecutionContext(
            $input,
            $output
        );

        $this->loadGameData();

        $this->syncGuild($context, 'La Danse Macabre', 'Defias Brotherhood');
        $this->syncGuild($context, 'La Danse Macabre', 'Darkmoon Faire');
    }

    private function loadGameData()
    {
        /** @var GameDataService $gameDataService */
        $gameDataService = $this->getContainer()->get(GameDataService::SERVICE_NAME);

        $this->gameRaces   = $gameDataService->getAllGameRaces();
        $this->gameClasses = $gameDataService->getAllGameClasses();
        $this->guilds      = $gameDataService->getAllGuilds();
        $this->realms      = $gameDataService->getAllRealms();
    }

    private function syncGuild(CommandExecutionContext $context, string $guildName, string $realmName)
    {
        /** @var CharacterService $characterService */
        $characterService = $this->getContainer()->get(CharacterService::SERVICE_NAME);

        $realm = $this->getRealmFromName($realmName);

        $guild = $this->getGuildFromName($guildName, $realm->getId());

        $armoryGuild = $this->getArmoryObjects($context, $guild, $realm);

        if (is_null($armoryGuild))
        {
            $context->error("Could not get Armory information");

            return -1;
        }
       
        $armoryObjects = [];

        foreach($armoryGuild->members as $entry)
        {
            // we have seen JSON from the Armory where not all properties were present
            if (property_exists($entry, "character")
                &&
                $this->verifyProperties($entry->character, ['name', 'class', 'race', 'level', 'guild', 'realm']))
            {
                $armoryObject = new ArmoryObject();

                $armoryObject
                    ->setName($entry->character->name)
                    ->setLevel($entry->character->level)
                    ->setClassArmoryId($entry->character->class)
                    ->setRaceArmoryId($entry->character->race)
                    ->setGuildName($entry->character->guild)
                    ->setRealmName($entry->character->realm);

                $armoryObjects[] = $armoryObject;
            }
        }

        usort(
            $armoryObjects,
            function ($a, $b)
            {
                /** @var ArmoryObject $a */
                /** @var ArmoryObject $b */
                return $this->compareCharacters($a->getName(), $a->getRealmName(), $b->getName(), $b->getRealmName());
            }
        );

        // second we fetch all the currently active guild member from the database

        $context->debug("Fetching guild members from the database");
        
        $characterDtos = $characterService->getAllCharactersInGuild(
            new StringReference($guild->getId())
        );

        $context->debug("Number of members in database " . count($characterDtos));

        usort(
            $characterDtos,
            function ($a, $b)
            {
                /** @var Character $a */
                /** @var Character $b */
                //return strcmp($a->getName(), $b->getName());
                return $this->compareCharacters(
                    $a->getName(),
                    $this->getRealmFromId($a->getRealmReference()->getId())->getName(),
                    $b->getName(),
                    $this->getRealmFromId($b->getRealmReference()->getId())->getName()
                );
            }
        );

        // below we use a classical algorithm that calculates the difference between two sorted lists

        $guildSyncSession = $characterService->createGuildSyncSession(
            new StringReference($guild->getId())
        );

        try
        {
            $context->debug("Comparing both lists ...");

            $dbIndex = 0;
            $armoryIndex = 0;

            while (($dbIndex < count($characterDtos) && ($armoryIndex < count($armoryObjects))))
            {
                /** @var Character $currentCharacterDto */
                $currentCharacterDto = $characterDtos[$dbIndex];

                /** @var ArmoryObject $currentArmoryObject */
                $currentArmoryObject = $armoryObjects[$armoryIndex];

                $context->info(
                    "Comparing database "
                    . $currentCharacterDto->getName()
                    . " with Armory "
                    . $currentArmoryObject->getName()
                );

                $charCompareResult = $this->compareCharacters(
                    $currentCharacterDto->getName(),
                    $this->getRealmFromId($currentCharacterDto->getRealmReference()->getId())->getName(),
                    $currentArmoryObject->getName(),
                    $currentArmoryObject->getRealmName()
                );

                if ($charCompareResult == 0)
                {
                    // if character is the same as the next character from armory, do nothing
                    $context->info("Character already in our database " . $currentCharacterDto->getName());

                    if ($this->hasCharacterChanged($currentCharacterDto, $currentArmoryObject)) {
                        $context->info(
                            "Character changed in Armory, updating "
                            . $currentCharacterDto->getName()
                            . " "
                            . $currentCharacterDto->getId()
                        );

                        $guildSyncSession->addMessage(
                            "Character changed in Armory, updating "
                            . $currentCharacterDto->getName()
                            . " "
                            . $currentCharacterDto->getId()
                        );

                        $patchCharacter = new PatchCharacter();
                        $patchCharacter
                            ->setName($currentCharacterDto->getName())
                            ->setLevel($currentArmoryObject->getLevel())
                            ->setGameClassReference(
                                new StringReference($this->getGameClassFromArmoryId($currentArmoryObject->getClassArmoryId())->getId())
                            )
                            ->setGameRaceReference(
                                new StringReference($this->getGameRaceFromArmoryId($currentArmoryObject->getRaceArmoryId())->getId())
                            )
                            ->setRealmReference(
                                new StringReference($this->getRealmFromName($currentArmoryObject->getRealmName())->getId())
                            )
                            ->setGuildReference(
                                new StringReference($guild->getId())
                            );

                        $characterService->patchCharacter($guildSyncSession, $currentCharacterDto->getId(), $patchCharacter);
                    }

                    $armoryIndex++;
                    $dbIndex++;
                }
                elseif ($charCompareResult < 0)
                {
                    // if character comes before the current character from armory, it means
                    // the character isn't in the guild any more, end it

                    $context->info("Character is not in the guild anymore " . $currentCharacterDto->getName());

                    $guildSyncSession->addMessage("Character is not in the guild anymore " . $currentCharacterDto->getName());

                    $patchCharacter = new PatchCharacter();
                    $patchCharacter
                        ->setName($currentCharacterDto->getName())
                        ->setLevel($currentArmoryObject->getLevel())
                        ->setGameClassReference($currentCharacterDto->getGameClassReference())
                        ->setGameRaceReference($currentCharacterDto->getGameRaceReference())
                        ->setRealmReference($currentCharacterDto->getRealmReference());

                    $characterService->patchCharacter($guildSyncSession, $currentCharacterDto->getId(), $patchCharacter);

                    $characterService->untrackCharacter($guildSyncSession, $currentCharacterDto->getId());

                    $dbIndex++;
                }
                else
                {
                    // if character comes after the current character from armory, it means
                    // the armory has new characters, import them

                    $context->info("Character is not yet in database, importing " . $currentArmoryObject->getName());

                    $guildSyncSession->addMessage("Character is not yet in database, importing " . $currentArmoryObject->getName());

                    $patchCharacter = new PatchCharacter();
                    $patchCharacter
                        ->setName($currentArmoryObject->getName())
                        ->setLevel($currentArmoryObject->getLevel())
                        ->setGameClassReference(
                            new StringReference($this->getGameClassFromArmoryId($currentArmoryObject->getClassArmoryId())->getId())
                        )
                        ->setGameRaceReference(
                            new StringReference($this->getGameRaceFromArmoryId($currentArmoryObject->getRaceArmoryId())->getId())
                        )
                        ->setRealmReference(
                            new StringReference($this->getRealmFromName($currentArmoryObject->getRealmName())->getId())
                        )
                        ->setGuildReference(
                            new StringReference($guild->getId())
                        );

                    $characterService->trackCharacter($guildSyncSession, $patchCharacter);

                    $armoryIndex++;
                }
            }

            $context->info("Finished comparing database with armory, now processing left overs");

            // if we have any left overs, they are characters in the database
            // that are not in the guild anymore, let's end them
            while ($dbIndex < count($characterDtos))
            {
                /** @var Character $currentCharacterDto */
                $currentCharacterDto = $characterDtos[$dbIndex];

                $context->error("Character is not in the guild anymore, ending " . $currentCharacterDto->getName());

                $guildSyncSession->addMessage("Character is not in the guild anymore, ending " . $currentCharacterDto->getName());

                $patchCharacter = new PatchCharacter();
                $patchCharacter
                    ->setName($currentCharacterDto->getName())
                    ->setLevel($currentCharacterDto->getLevel())
                    ->setGameClassReference($currentCharacterDto->getGameClassReference())
                    ->setGameRaceReference($currentCharacterDto->getGameRaceReference())
                    ->setRealmReference($currentCharacterDto->getRealmReference());

                $characterService->patchCharacter($guildSyncSession, $currentCharacterDto->getId(), $patchCharacter);

                $characterService->untrackCharacter($guildSyncSession, $currentCharacterDto->getId());

                $dbIndex++;
            }

            // if we have any left overs, they are all characters in the guild
            // according to the Armory but that are not yet in the database,
            // let's add them
            while ($armoryIndex < count($armoryObjects))
            {
                $currentArmoryObject = $armoryObjects[$armoryIndex];

                $context->error("Character is not yet in database, importing " . $currentArmoryObject->getName());

                $guildSyncSession->addMessage("Character is not yet in database, importing " . $currentArmoryObject->getName());

                $patchCharacter = new PatchCharacter();
                $patchCharacter
                    ->setName($currentArmoryObject->getName())
                    ->setLevel($currentArmoryObject->getLevel())
                    ->setGameClassReference(
                        new StringReference($this->getGameClassFromArmoryId($currentArmoryObject->getClassArmoryId())->getId())
                    )
                    ->setGameRaceReference(
                        new StringReference($this->getGameRaceFromArmoryId($currentArmoryObject->getRaceArmoryId())->getId())
                    )
                    ->setRealmReference(
                        new StringReference($this->getRealmFromName($currentArmoryObject->getRealmName())->getId())
                    )
                    ->setGuildReference(
                        new StringReference($guild->getId())
                    );

                $characterService->trackCharacter($guildSyncSession, $patchCharacter);

                $armoryIndex++;
            }
        }
        catch(\Exception $exception)
        {
            $context->error("Exception while updating characters " . $exception);
            $context->error($exception->getTraceAsString());
            
            $guildSyncSession->addMessage("Caught exception - " . $exception->getMessage());
        }
        finally
        {
            $characterService->endCharacterSession($guildSyncSession);
        }


        return 0;
    }

    protected function compareCharacters(string $charOneName, string $charOneRealm, string $charTwoName, string $charTwoRealm)
    {
        $charOneCombinedName = $charOneName . "-" . $charOneRealm;
        $charTwoCombinedName = $charTwoName . "-" . $charTwoRealm;

        return strcmp($charOneCombinedName, $charTwoCombinedName);
    }

    /**
     * @param $dbCharacter
     * @param $armoryCharacter
     * @return bool
     */
    protected function hasCharacterChanged(Character $dbCharacter, ArmoryObject $armoryCharacter)
    {
        if ($dbCharacter->getLevel() != $armoryCharacter->getLevel())
        {
            return true;
        }

        if (strcmp($dbCharacter->getRealmReference(), $this->getRealmFromName($armoryCharacter->getRealmName())->getId()) != 0)
        {
            return true;
        }

        if (strcmp($dbCharacter->getGameClassReference(), $this->getGameClassFromArmoryId($armoryCharacter->getClassArmoryId())->getId()) != 0)
        {
            return true;
        }

        if (strcmp($dbCharacter->getGameRaceReference(), $this->getGameRaceFromArmoryId($armoryCharacter->getRaceArmoryId())->getId()) != 0)
        {
            return true;
        }

        return false;
    }

    /**
     * @param CommandExecutionContext $context
     * @param Guild $guild
     * @param Realm $realm
     *
     * @return null|array
     */
    protected function getArmoryObjects(CommandExecutionContext $context, Guild $guild, Realm $realm)
    {
        $armoryUrl = "https://eu.api.battle.net/wow/guild/"
            . rawurlencode($realm->getName())
            . "/"
            . rawurlencode($guild->getName())
            . "?fields=members&locale=en_GB&apikey="
            . $this->getContainer()->getParameter("battlenet_key");

        try
        {
            $context->info("Fetching guild members from the Armory");
            $context->info("Armory URL " . $armoryUrl);

            $json = file_get_contents($armoryUrl);

            $context->debug("Armory returned " . $json);

            if (is_null($json))
            {
                $context->error("Armory URL returned empty content");

                return null;
            }

            $armoryGuild = json_decode($json);

            if (is_null($armoryGuild))
            {
                $context->error("Could not decode Armory data into objects");
                $context->error($json);

                return null;
            }
            elseif (!property_exists($armoryGuild, "battlegroup") || !property_exists($armoryGuild, "realm"))
            {
                $context->error("Armory did not return list of members");
                $context->error($json);

                return null;
            }

            return $armoryGuild;
        }
        catch(\Exception $e)
        {
            $context->error("Exception while fetching Armory data " . $e);

            return null;
        }
    }

    private function verifyProperties($object, $propertyNames)
    {
        $hasAllProperties = true;

        foreach($propertyNames as $propertyName)
        {
            $hasAllProperties = $hasAllProperties && property_exists($object, $propertyName);
        }

        return $hasAllProperties;
    }

    /**
     * @param int $armoryId
     * @return GameClass
     * @throws \Exception
     */
    private function getGameClassFromArmoryId(int $armoryId)
    {
        foreach($this->gameClasses as $gameClass)
        {
            /** @var GameClass $gameClass */

            if ($armoryId == $gameClass->getArmoryId())
            {
                return $gameClass;
            }
        }

        throw new \Exception("Could not find GameClass for Armory id " . $armoryId);
    }

    /**
     * @param int $armoryId
     * @return GameRace
     * @throws \Exception
     */
    private function getGameRaceFromArmoryId(int $armoryId)
    {
        foreach($this->gameRaces as $gameRace)
        {
            /** @var GameRace $gameRace */

            if ($armoryId == $gameRace->getArmoryId())
            {
                return $gameRace;
            }
        }

        throw new \Exception("Could not find GameRace for Armory id " . $armoryId);
    }

    /**
     * @param string $realmName
     * @return Realm|null
     */
    private function getRealmFromName(string $realmName)
    {
        foreach($this->realms as $realm)
        {
            /** @var Realm $realm */

            if ($realmName == $realm->getName())
            {
                return $realm;
            }
        }

        /** @var GameDataService $gameDataService */
        $gameDataService = $this->getContainer()->get(GameDataService::SERVICE_NAME);

        $patchRealm = new PatchRealm();
        $patchRealm->setName($realmName);

        $realmDto = $gameDataService->postRealm($patchRealm);

        $this->realms[] = $realmDto;

        return $realmDto;
    }

    /**
     * @param string $realmId
     * @return Realm|null
     */
    private function getRealmFromId(string $realmId)
    {
        foreach($this->realms as $realm)
        {
            /** @var Realm $realm */

            if ($realmId == $realm->getId())
            {
                return $realm;
            }
        }

        return null;
    }

    /**
     * @param string $guildName
     * @param string $realmId
     * @return Guild|null
     */
    private function getGuildFromName(string $guildName, string $realmId)
    {
        foreach($this->guilds as $guild)
        {
            /** @var Guild $guild */

            if (($guildName == $guild->getName())
                && ($guild->getRealmReference()->getId() == $realmId))
            {
                return $guild;
            }
        }

        /** @var GameDataService $gameDataService */
        $gameDataService = $this->getContainer()->get(GameDataService::SERVICE_NAME);

        $patchGuild = new PatchGuild();
        $patchGuild->setName($guildName);
        $patchGuild->setRealmId(new StringReference($realmId));

        $guildDto = $gameDataService->postGuild($patchGuild);

        $this->guilds[] = $guildDto;

        return $guildDto;
    }
}

