<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Command\Armory;

use Doctrine\DBAL\ConnectionException;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use LaDanse\ServicesBundle\Common\CommandExecutionContext;
use LaDanse\ServicesBundle\Service\Character\CharacterSession;
use LaDanse\ServicesBundle\Service\DTO\Character\Character;
use LaDanse\ServicesBundle\Service\DTO\Character\PatchCharacter;
use LaDanse\ServicesBundle\Service\DTO\GameData\GameClass;
use LaDanse\ServicesBundle\Service\DTO\GameData\GameRace;
use LaDanse\ServicesBundle\Service\DTO\GameData\Guild;
use LaDanse\ServicesBundle\Service\DTO\GameData\Realm;
use LaDanse\ServicesBundle\Service\DTO\Reference\StringReference;
use LaDanse\ServicesBundle\Service\GameData\GameDataService;
use LaDanse\ServicesBundle\Service\Character\CharacterService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @noinspection PhpUnused
 */
class RefreshGuildMembersCommand extends ContainerAwareCommand
{
    /**
     * @var CommandExecutionContext
     */
    private $context;

    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var CharacterService
     */
    private $characterService;

    /**
     * @var string
     */
    private $accessToken;

    private $gameRaces;
    private $gameClasses;
    private $guilds;
    private $realms;

    /**
     * @return void
     *
     * @noinspection PhpUnused
     */
    protected function configure()
    {
        $this
            ->setName('ladanse:refreshGuildMembers')
            ->setDescription('Refresh guild members from the armory');
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     *
     * @throws ConnectionException
     * @throws Exception
     *
     * @noinspection PhpUnused
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->doctrine = $this->getContainer()->get('doctrine');
        $this->entityManager = $this->doctrine->getManager();
        $this->characterService = $this->getContainer()->get(CharacterService::SERVICE_NAME);

        $this->context = new CommandExecutionContext(
            $input,
            $output
        );

        $this->entityManager->getConnection()->beginTransaction();
        try
        {
            $this->accessToken = BattleNetUtils::getBlizzardAccessToken(
                $this->context,
                $this->getContainer()->getParameter("battlenet_key"),
                $this->getContainer()->getParameter("battlenet_secret"));

            $this->loadGameData();

            $this->syncGuild('La Danse Macabre', 'Defias Brotherhood');
            $this->syncGuild('La Danse Macabre', 'Darkmoon Faire');
            $this->syncGuild('La Danse Macabré', 'Defias Brotherhood');

            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();
        }
        catch (Exception $e)
        {
            $this->entityManager->getConnection()->rollBack();

            throw $e;
        }
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

    /**
     * @param string $guildName
     * @param string $realmName
     *
     * @return int
     *
     * @throws Exception
     */
    private function syncGuild(string $guildName, string $realmName)
    {
        $realm = $this->getRealmFromName($realmName);

        $guild = $this->getGuildFromName($realm->getId(), $guildName);

        $armoryGuild = new GuildWrapper($this->getArmoryObjects($guild, $realm));

        if (is_null($armoryGuild))
        {
            $this->context->error("Could not get Armory information");

            return -1;
        }

        $armoryCharacters = [];

        foreach($armoryGuild->getMembers() as $entry)
        {
            $armoryCharacters[] = new CharacterWrapper($entry);
        }

        usort(
            $armoryCharacters,
            function ($a, $b)
            {
                /** @var CharacterWrapper $a */
                /** @var CharacterWrapper $b */
                return $this->compareCharacters($a->getName(), $a->getRealmGameId(), $b->getName(), $b->getRealmGameId());
            }
        );

        // second we fetch all the currently active guild member from the database

        $this->context->debug("Fetching guild members from the database");
        
        $characterDtos = $this->characterService->getAllCharactersInGuild(
            new StringReference($guild->getId())
        );

        $this->context->debug("Number of members in database " . count($characterDtos));

        usort(
            $characterDtos,
            function ($a, $b)
            {
                /** @var Character $a */
                /** @var Character $b */
                //return strcmp($a->getName(), $b->getName());
                return $this->compareCharacters(
                    $a->getName(),
                    $a->getRealmReference()->getId(),
                    $b->getName(),
                    $b->getRealmReference()->getId()
                );
            }
        );

        // below we use a classical algorithm that calculates the difference between two sorted lists

        $guildSyncSession = $this->characterService->createGuildSyncSession(new StringReference($guild->getId()));

        try
        {
            $this->context->debug("Comparing both lists ...");

            $dbIndex = 0;
            $armoryIndex = 0;

            while (($dbIndex < count($characterDtos) && ($armoryIndex < count($armoryCharacters))))
            {
                /** @var Character $currentCharacterDto */
                $currentCharacterDto = $characterDtos[$dbIndex];

                /** @var CharacterWrapper $currentArmoryCharacter */
                $currentArmoryCharacter = $armoryCharacters[$armoryIndex];

                $this->context->info(
                    "Comparing database "
                    . $currentCharacterDto->getName()
                    . " with Armory "
                    . $currentArmoryCharacter->getName()
                );

                $charCompareResult = $this->compareCharacters(
                    $currentCharacterDto->getName(),
                    $currentCharacterDto->getRealmReference()->getId(),
                    $currentArmoryCharacter->getName(),
                    $this->getRealmDTO($currentArmoryCharacter)->getId()
                );

                if ($charCompareResult == 0)
                {
                    // if character is the same as the next character from armory, do nothing
                    $this->context->info("Character already in our database " . $currentCharacterDto->getName());

                    if ($this->hasCharacterChanged($currentCharacterDto, $currentArmoryCharacter))
                    {
                        $this->context->info(
                            "Character changed in Armory, updating "
                            . $currentCharacterDto->getName()
                            . " "
                            . $currentCharacterDto->getId()
                        );

                        $this->updateCharacter(
                            $currentCharacterDto,
                            $guildSyncSession,
                            $currentArmoryCharacter,
                            $guild);
                    }

                    $armoryIndex++;
                    $dbIndex++;
                }
                elseif ($charCompareResult < 0)
                {
                    // if character comes before the current character from armory, it means
                    // the character isn't in the guild any more, end it

                    $this->context->info("Character is not in the guild anymore " . $currentCharacterDto->getName());

                    $this->removeCharacter(
                        $guildSyncSession,
                        $currentCharacterDto);

                    $dbIndex++;
                }
                else
                {
                    // if character comes after the current character from armory, it means
                    // the armory has new characters, import them

                    $this->context->info("Character is not yet in database, importing " . $currentArmoryCharacter->getName());

                    $this->addCharacter($guildSyncSession, $currentArmoryCharacter, $guild);

                    $armoryIndex++;
                }
            }

            $this->context->info("Finished comparing database with armory, now processing left-overs");

            // if we have any left overs in $characterDtos, they are characters in the database
            // that are not in the guild anymore, let's end them
            while ($dbIndex < count($characterDtos))
            {
                /** @var Character $currentCharacterDto */
                $currentCharacterDto = $characterDtos[$dbIndex];

                $this->context->error("Character is not in the guild anymore, ending " . $currentCharacterDto->getName());

                $this->removeCharacter(
                    $guildSyncSession,
                    $currentCharacterDto);

                $dbIndex++;
            }

            // if we have any left overs in $armoryCharacters, they are all characters in the guild
            // according to the Armory but that are not yet in the database, let's add them
            while ($armoryIndex < count($armoryCharacters))
            {
                $currentArmoryCharacter = new CharacterWrapper($armoryCharacters[$armoryIndex]);

                $this->context->error("Character is not yet in database, importing " . $currentArmoryCharacter->getName());

                $this->addCharacter(
                    $guildSyncSession,
                    $currentArmoryCharacter,
                    $guild);

                $armoryIndex++;
            }
        }
        catch(Exception $exception)
        {
            $this->context->error("Exception while updating characters " . $exception);
            $this->context->error($exception->getTraceAsString());
            
            $guildSyncSession->addMessage("Caught exception - " . $exception->getMessage());

            throw $exception;
        }
        finally
        {
            $this->characterService->endCharacterSession($guildSyncSession);
        }

        return 0;
    }

    /**
     * @param string $charOneName
     * @param string $charOneRealmId
     * @param string $charTwoName
     * @param string $charTwoRealmId
     *
     * @return int
     */
    protected function compareCharacters(string $charOneName, string $charOneRealmId, string $charTwoName, string $charTwoRealmId)
    {
        $charOneCombinedName = $charOneName . "-" . strval($charOneRealmId);
        $charTwoCombinedName = $charTwoName . "-" . strval($charTwoRealmId);

        return strcmp($charOneCombinedName, $charTwoCombinedName);
    }

    /**
     * @param Character $dbCharacter
     * @param CharacterWrapper $armoryCharacter
     *
     * @return bool
     *
     * @throws Exception
     */
    protected function hasCharacterChanged(Character $dbCharacter, CharacterWrapper $armoryCharacter)
    {
        if ($dbCharacter->getLevel() != $armoryCharacter->getLevel())
        {
            return true;
        }

        if (strcmp($dbCharacter->getRealmReference(), $this->getRealmDTO($armoryCharacter)->getId()) != 0)
        {
            return true;
        }

        if (strcmp($dbCharacter->getGameClassReference(), $this->getGameClassDTO($armoryCharacter)->getId()) != 0)
        {
            return true;
        }

        if (strcmp($dbCharacter->getGameRaceReference(), $this->getGameRaceDTO($armoryCharacter)->getId()) != 0)
        {
            return true;
        }

        return false;
    }

    /**
     * @param Guild $guild
     * @param Realm $realm
     *
     * @return null|array
     * @throws Exception
     */
    protected function getArmoryObjects(Guild $guild, Realm $realm)
    {
        try
        {
            $this->context->info(sprintf("Fetching guild members from the Armory for guild '%s' on realm '%s'",
                $guild->getName(),
                $realm->getName()
            ));

            $guildMembers = $this->getBlizzardGuildMembers($guild, $realm);

            $json = json_encode($guildMembers);

            $this->context->debug("Armory returned " . $json);

            if (is_null($guildMembers))
            {
                $this->context->error("Armory URL returned empty content");

                throw new Exception("Armory URL returned empty content");
            }

            $armoryGuild = $guildMembers;

            if (is_null($armoryGuild))
            {
                $this->context->error("Could not decode Armory data into objects");
                $this->context->error($json);

                throw new Exception("Could not decode Armory data into objects");
            }
            elseif (!property_exists($armoryGuild, "guild") || !property_exists($armoryGuild, "members"))
            {
                $this->context->error("Armory did not return list of members");
                $this->context->error($json);

                throw new Exception("Armory did not return list of members");
            }

            return $armoryGuild;
        }
        catch(Exception $e)
        {
            $this->context->error("Exception while fetching Armory data " . $e);

            throw $e;
        }
    }

    /**
     * @param Guild $guild
     * @param Realm $realm
     *
     * @return mixed
     */
    protected function getBlizzardGuildMembers(Guild $guild, Realm $realm)
    {
        $endpointUrl = "https://eu.api.blizzard.com/data/wow/guild/"
            . BattleNetUtils::sluggify($realm->getName())
            . "/" . BattleNetUtils::sluggify($guild->getName())
            . "/roster";

        return BattleNetUtils::callBattleNetAPI($this->context, $this->accessToken, 'profile', $endpointUrl);
    }

    /**
     * @param CharacterWrapper $armoryCharacter
     *
     * @return GameClass
     *
     * @throws Exception
     */
    private function getGameClassDTO(CharacterWrapper $armoryCharacter)
    {
        foreach($this->gameClasses as $gameClass)
        {
            /** @var GameClass $gameClass */

            if ($armoryCharacter->getClassGameId() == $gameClass->getArmoryId())
            {
                return $gameClass;
            }
        }

        throw new Exception("Could not find GameClass for Armory id " . $armoryCharacter->getClassGameId());
    }

    /**
     * @param CharacterWrapper $armoryCharacter
     *
     * @return GameRace
     *
     * @throws Exception
     */
    private function getGameRaceDTO(CharacterWrapper $armoryCharacter)
    {
        foreach($this->gameRaces as $gameRace)
        {
            /** @var GameRace $gameRace */

            if ($armoryCharacter->getRaceGameId() == $gameRace->getArmoryId())
            {
                return $gameRace;
            }
        }

        throw new Exception("Could not find GameRace for Armory id " . $armoryCharacter->getRaceGameId());
    }

    /**
     * @param CharacterWrapper $armoryCharacter
     *
     * @return Realm|null
     *
     * @throws Exception
     */
    private function getRealmDTO(CharacterWrapper $armoryCharacter)
    {
        foreach($this->realms as $realm)
        {
            /** @var Realm $realm */

            if ($armoryCharacter->getRealmGameId() == $realm->getGameId())
            {
                return $realm;
            }
        }

        /** @var GameDataService $gameDataService */
        //$gameDataService = $this->getContainer()->get(GameDataService::SERVICE_NAME);

        throw new Exception('Creating a realm on the fly is not implemented yet - ' . $armoryCharacter->getRealmSlug());

        /*
        $patchRealm = new PatchRealm();
        $patchRealm->setGameId($armoryCharacter->getRealmGameId());
        $patchRealm->setName($realmName);

        $realmDto = $gameDataService->postRealm($patchRealm);

        $this->realms[] = $realmDto;

        return $realmDto;
        */
    }

    /**
     * @param string $realmName
     * @return Realm|null
     *
     * @throws Exception
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
        //$gameDataService = $this->getContainer()->get(GameDataService::SERVICE_NAME);

        throw new Exception('Creating a realm on the fly is not implemented yet - ' . $realmName);

        /*
        $patchRealm = new PatchRealm();
        $patchRealm->setGameId($armoryCharacter->getRealmGameId());
        $patchRealm->setName($realmName);

        $realmDto = $gameDataService->postRealm($patchRealm);

        $this->realms[] = $realmDto;

        return $realmDto;
        */
    }

    /**
     * @param string $realmId
     * @param string $guildName
     *
     * @return Guild|null
     *
     * @throws Exception
     */
    private function getGuildFromName(string $realmId, string $guildName)
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
        //$gameDataService = $this->getContainer()->get(GameDataService::SERVICE_NAME);

        throw new Exception('Creating a guild on the fly is not implemented yet - ' . $guildName);

        /*
        $patchGuild = new PatchGuild();
        $patchGuild->setName($guildName);
        $patchGuild->setRealmId(new StringReference($realmId));

        $guildDto = $gameDataService->postGuild($patchGuild);

        $this->guilds[] = $guildDto;

        return $guildDto;
        */
    }

    /**
     * @param Character $currentCharacterDto
     * @param CharacterSession $guildSyncSession
     * @param CharacterWrapper $currentArmoryCharacter
     * @param Guild $guild
     *
     * @throws Exception
     */
    private function updateCharacter(
        Character $currentCharacterDto,
        CharacterSession $guildSyncSession,
        CharacterWrapper $currentArmoryCharacter,
        Guild $guild)
    {
        $guildSyncSession->addMessage(
            "Character changed in Armory, updating "
            . $currentCharacterDto->getName()
            . " "
            . $currentCharacterDto->getId()
        );

        $patchCharacter = new PatchCharacter();
        $patchCharacter
            ->setName($currentCharacterDto->getName())
            ->setLevel($currentArmoryCharacter->getLevel())
            ->setGameClassReference(
                new StringReference($this->getGameClassDTO($currentArmoryCharacter)->getId())
            )
            ->setGameRaceReference(
                new StringReference($this->getGameRaceDTO($currentArmoryCharacter)->getId())
            )
            ->setRealmReference(
                new StringReference($this->getRealmDTO($currentArmoryCharacter)->getId())
            )
            ->setGuildReference(
                new StringReference($guild->getId())
            );

        // DISABLED TO TEST
        $this->characterService->patchCharacter($guildSyncSession, $currentCharacterDto->getId(), $patchCharacter);
    }

    /**
     * @param CharacterSession $guildSyncSession
     * @param Character $currentCharacterDto
     *
     * @throws Exception
     */
    private function removeCharacter(
        CharacterSession $guildSyncSession,
        Character $currentCharacterDto)
    {
        $guildSyncSession->addMessage("Character is not in the guild anymore " . $currentCharacterDto->getName());

        $patchCharacter = new PatchCharacter();
        $patchCharacter
            ->setName($currentCharacterDto->getName())
            ->setLevel($currentCharacterDto->getLevel())
            ->setGameClassReference($currentCharacterDto->getGameClassReference())
            ->setGameRaceReference($currentCharacterDto->getGameRaceReference())
            ->setRealmReference($currentCharacterDto->getRealmReference());

        // DISABLED TO TEST
        $this->characterService->patchCharacter($guildSyncSession, $currentCharacterDto->getId(), $patchCharacter);

        // DISABLED TO TEST
        $this->characterService->untrackCharacter($guildSyncSession, $currentCharacterDto->getId());
    }

    /**
     * @param CharacterSession $guildSyncSession
     * @param CharacterWrapper $currentArmoryCharacter
     * @param Guild $guild
     *
     * @throws Exception
     */
    private function addCharacter(
        CharacterSession $guildSyncSession,
        CharacterWrapper $currentArmoryCharacter,
        Guild $guild)
    {
        $guildSyncSession->addMessage("Character is not yet in database, importing " . $currentArmoryCharacter->getName());

        $patchCharacter = new PatchCharacter();
        $patchCharacter
            ->setName($currentArmoryCharacter->getName())
            ->setLevel($currentArmoryCharacter->getLevel())
            ->setGameClassReference(
                new StringReference($this->getGameClassDTO($currentArmoryCharacter)->getId())
            )
            ->setGameRaceReference(
                new StringReference($this->getGameRaceDTO($currentArmoryCharacter)->getId())
            )
            ->setRealmReference(
                new StringReference($this->getRealmDTO($currentArmoryCharacter)->getId())
            )
            ->setGuildReference(
                new StringReference($guild->getId())
            );

        // DISABLED TO TEST
        $this->characterService->trackCharacter($guildSyncSession, $patchCharacter);
    }
}

