<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Command;

use LaDanse\ServicesBundle\Common\CommandExecutionContext;
use LaDanse\ServicesBundle\Service\GameData\GameDataService;
use LaDanse\ServicesBundle\Service\GuildCharacter\GuildCharacterService;
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

        $armoryGuild = $this->getArmoryObjects($context);

        if (is_null($armoryGuild))
        {
            $context->error("Could not get Armory information");

            return;
        }
       
        $armoryNames = array();

        foreach($armoryGuild->members as $entry)
        {
            $armoryNames[] = (object) array(
                "name"  => $entry->character->name,
                "class" => $entry->character->class,
                "race"  => $entry->character->race,
                "level" => $entry->character->level
            );
        }

        usort(
            $armoryNames,
            function ($a, $b) {
                return strcmp($a->name, $b->name);
            }
        );

        // second we fetch all the currently active guild member from the database

        $context->debug("Fetching guild members from the database");

        /** @var GuildCharacterService $characterService */
        $characterService = $this->getContainer()->get(GuildCharacterService::SERVICE_NAME);

        /** @var GameDataService $gameDataService */
        $gameDataService = $this->getContainer()->get(GameDataService::SERVICE_NAME);

        $gameRaces = $gameDataService->getAllRaces();
        $gameClasses = $gameDataService->getAllClasses();
        
        $dbNames = $characterService->getAllGuildCharacters();

        $context->debug("Number of members in database " . count($dbNames));

        usort(
            $dbNames,
            function ($a, $b) {
                return strcmp($a->name, $b->name);
            }
        );

        // below we use a classical algoritm that calculates the difference
        // between two sorted lists, acting accordingly on any difference found

        $context->debug("Comparing both lists ...");

        $dbIndex = 0;
        $armoryIndex = 0;

        while(($dbIndex < count($dbNames) && ($armoryIndex < count($armoryNames))))
        {
            $dbName = $dbNames[$dbIndex]->name;
            $armoryName = $armoryNames[$armoryIndex]->name;

            if (strcmp($dbName, $armoryName) == 0)
            {
                // if character is the same as the next character from armory, do nothing
                $context->info("Character already in our database " . $dbName);

                if ($this->hasCharacterChanged($dbNames[$dbIndex], $armoryNames[$armoryIndex]))
                {
                    $context->info(
                        "Character changed in Armory, updating " . $dbName . " " . $dbNames[$dbIndex]->id
                    );

                    $this->updateCharacter(
                        $dbNames[$dbIndex]->id,
                        $armoryNames[$armoryIndex],
                        $this->getGameRace($gameRaces, $armoryNames[$armoryIndex]->race),
                        $this->getGameClass($gameClasses, $armoryNames[$armoryIndex]->class)
                    );
                }

                $armoryIndex++;
                $dbIndex++;
            }
            elseif (strcmp($dbName, $armoryName) < 0)
            {
                // if character comes before the current character from armory, it means
                // the character isn't in the guild any more, end it
                $context->info("Character is not in the guild anymore " . $dbName);

                $this->endCharacter($dbNames[$dbIndex]->id);

                $dbIndex++;
            }
            else
            {
                // if character comes after the current character from armory, it means
                // the armory has new characters, import them
                $context->info("Character is not yet in database, importing " . $armoryName);

                $this->importCharacter(
                    $armoryNames[$armoryIndex],
                    $this->getGameRace($gameRaces, $armoryNames[$armoryIndex]->race),
                    $this->getGameClass($gameClasses, $armoryNames[$armoryIndex]->class)
                );

                $armoryIndex++;
            }
        }

        // if we have any left overs, they are characters in the database
        // that are not anymore in the guild, let's end them
        while($dbIndex < count($dbNames))
        {
            $dbName = $dbNames[$dbIndex]->name;

            $context->info("Character is not in the guild anymore, ending " . $dbName);

            $this->endCharacter($dbNames[$dbIndex]->id);

            $dbIndex++;
        }

        // if we have any left overs, they are all characters in the guild
        // according to the Armory but that are not yet in the database,
        // let's add them
        while($armoryIndex < count($armoryNames))
        {
            $armoryName = $armoryNames[$armoryIndex]->name;

            // the amory has new characters, import it
            $context->info("Character is not yet in database, importing " . $armoryName);

            $this->importCharacter(
                $armoryNames[$armoryIndex],
                $this->getGameRace($gameRaces, $armoryNames[$armoryIndex]->race),
                $this->getGameClass($gameClasses, $armoryNames[$armoryIndex]->class)
            );

            $armoryIndex++;
        }
    }

    /**
     * @param $characterId
     */
    protected function endCharacter($characterId)
    {
        $guildCharacterService = $this->getContainer()->get(GuildCharacterService::SERVICE_NAME);

        $guildCharacterService->endCharacter($characterId);
    }

    /**
     * @param $dbCharacter
     * @param $armoryCharacter
     * @return bool
     */
    protected function hasCharacterChanged($dbCharacter, $armoryCharacter)
    {
        if ($dbCharacter->level != $armoryCharacter->level)
        {
            return true;
        }

        if (strcmp($dbCharacter->name, $armoryCharacter->name) != 0)
        {
            return true;
        }

        return false;
    }

    /**
     * @param $id
     * @param $armoryCharacter
     * @param $gameRace
     * @param $gameClass
     */
    protected function updateCharacter($id, $armoryCharacter, $gameRace, $gameClass)
    {
        $guildCharacterService = $this->getContainer()->get(GuildCharacterService::SERVICE_NAME);

        $guildCharacterService->updateCharacter(
            $id,
            $armoryCharacter->name,
            $armoryCharacter->level,
            $gameRace,
            $gameClass,
            'La Danse Macabre'
        );
    }

    /**
     * @param $armoryCharacter
     * @param $gameRace
     * @param $gameClass
     */
    protected function importCharacter($armoryCharacter, $gameRace, $gameClass)
    {
        /** @var $guildCharacterService GuildCharacterService */
        $guildCharacterService = $this->getContainer()->get(GuildCharacterService::SERVICE_NAME);

        $guildCharacterService->createCharacter(
            $armoryCharacter->name,
            $armoryCharacter->level,
            $gameRace,
            $gameClass,
            'La Danse Macabre',
            'Defias Brotherhood'
        );
    }

    /**
     * @param $gameRaces
     * @param $gameRaceId
     * @return \LaDanse\DomainBundle\Entity\GameRace|null
     */
    protected function getGameRace($gameRaces, $gameRaceId)
    {
        /* @var $gameRace \LaDanse\DomainBundle\Entity\GameRace */
        foreach($gameRaces as $gameRace)
        {
            if ($gameRace->getId() == $gameRaceId)
            {
                return $gameRace;
            }
        }

        return null;
    }

    /**
     * @param $gameClasses
     * @param $gameClassId
     * @return \LaDanse\DomainBundle\Entity\GameClass|null
     */
    protected function getGameClass($gameClasses, $gameClassId)
    {
        /* @var $gameClass \LaDanse\DomainBundle\Entity\GameClass */
        foreach($gameClasses as $gameClass)
        {
            if ($gameClass->getId() == $gameClassId)
            {
                return $gameClass;
            }
        }

        return null;
    }

    /**
     * @param CommandExecutionContext $context
     *
     * @return object|null
     */
    protected function getArmoryObjects(CommandExecutionContext $context)
    {
        try
        {
            $context->debug("Fetching guild members from the Armory");

            $apiKey = $this->getContainer()->getParameter("battlenet_key");
            $fullUrl = RefreshGuildMembersCommand::BATTLENET_API_URL . $apiKey;

            $json = file_get_contents($fullUrl);

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
            elseif (!property_exists($armoryGuild, "battlegroup") or !property_exists($armoryGuild, "realm"))
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
}