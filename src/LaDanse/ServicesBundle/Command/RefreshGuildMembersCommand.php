<?php

namespace LaDanse\ServicesBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use LaDanse\ServicesBundle\Service\GuildCharacterService;

use LaDanse\DomainBundle\Entity\Character;

class RefreshGuildMembersCommand extends ContainerAwareCommand
{
    const ARMORY_URL = "http://eu.battle.net/api/wow/guild/Defias%20Brotherhood/La%20Danse%20Macabre?fields=members";

    const VERBOSE_OPTION = 'verbose';
    const DIAG_OPTION    = 'diag';

    protected function configure()
    {
        $this
            ->setName('ladanse:refreshGuildMember')
            ->setDescription('Refresh guild members from the armory')
            ->addOption(self::DIAG_OPTION, null, InputOption::VALUE_NONE, 'Print diagnostic messages')
            // the option "verbose" is by default present on commands
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // first we fetch all the guild members from the armory and we sort them

        $this->debug($input, $output, "Fetching guild members from the Armory");

        $json = file_get_contents(RefreshGuildMembersCommand::ARMORY_URL);

        $armoryGuild = json_decode($json);
       
        $armoryNames = array();

        foreach($armoryGuild->members as $entry)
        {
            $armoryNames[] = (object) array(
                "name"  => $entry->character->name
            );
        }        

        usort($armoryNames, function($a, $b)
            {
                return strcmp($a->name, $b->name);
            }
        );

        // second we fetch all the currently active guild member from the database

        $this->debug($input, $output, "Fetching guild members from the database");

        $em = $this->getContainer()->get('doctrine')->getManager();

        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery(
            'SELECT c ' .
            'FROM LaDanse\DomainBundle\Entity\Character c ' .
            'WHERE c.fromTime <= :onDateTime AND (c.endTime >= :onDateTime OR c.endTime IS NULL)');
        
        $query->setParameter('onDateTime', new \DateTime());
        
        $dbCharacters = $query->getResult();

        $dbNames = array();

        // we have to create our own array so we can sort it using
        // exactly the same sorting algoritm as we used to sort
        // the Armory data.
        // Note that MySQL and PHP sort a unicode array differently!
        foreach($dbCharacters as $dbCharacter)
        {
            $dbNames[] = (object) array(
                "id"    => $dbCharacter->getId(),
                "name"  => $dbCharacter->getName()
            );
        }

        usort($dbNames, function($a, $b)
            {
                return strcmp($a->name, $b->name);
            }
        );

        // below we use a classical algoritm that calculates the difference
        // between two sorted lists, acting accordingly on any difference found

        $this->debug($input, $output, "Comparing both lists ...");

        $dbIndex = 0;
        $armoryIndex = 0;

        while(($dbIndex < count($dbNames) && ($armoryIndex < count($armoryNames))))
        {
            $dbName = $dbNames[$dbIndex]->name;
            $armoryName = $armoryNames[$armoryIndex]->name;

            if (strcmp($dbName, $armoryName) == 0)
            {
                // if character is the same as the next character from armory, do nothing
                $this->debug($input, $output, "Character already in our database " . $dbName);
                
                $armoryIndex++; 
                $dbIndex++;                   
            }
            elseif (strcmp($dbName, $armoryName) < 0)
            {
                // if character comes before the current character from armory, it means
                // the character isn't in the guild any more, end it
                $this->info($input, $output, "Character is not in the guild anymore " . $dbName);

                $this->endCharacter($dbNames[$dbIndex]->id);

                $dbIndex++;
            }
            else
            {
                // if character comes after the current character from armory, it means 
                // the armory has new characters, import them
                $this->info($input, $output, "Character is not yet in database, importing " . $armoryName);

                $this->importCharacter($armoryName);

                $armoryIndex++; 
            }
        }

        // if we have any left overs, they are characters in the database
        // that are not anymore in the guild, let's end them
        while($dbIndex < count($dbNames))
        {
            $dbName = $dbNames[$dbIndex]->name;

            $this->info($input, $output, "Character is not in the guild anymore " . $dbName);

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
            $this->info($input, $output, "Character is not yet in database, importing " . $armoryName);

            $this->importCharacter($armoryName);

            $armoryIndex++;
        }
    }

    protected function endCharacter($characterId)
    {
        $guildCharacterService = $this->getContainer()->get(GuildCharacterService::SERVICE_NAME);

        $guildCharacterService->endCharacter($characterId);
    }

    protected function importCharacter($name)
    {
        $guildCharacterService = $this->getContainer()->get(GuildCharacterService::SERVICE_NAME);

        $guildCharacterService->importCharacter($name);
    }

    protected function debug(InputInterface $input, OutputInterface $output, $text)
    {
        if ($input->getOption(self::DIAG_OPTION))
        {
            $output->writeln($text);
        }
    }

    protected function info(InputInterface $input, OutputInterface $output, $text)
    {
        if ($input->getOption(self::VERBOSE_OPTION))
        {
            $output->writeln($text);
        }
    }
}