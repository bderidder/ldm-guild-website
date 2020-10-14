<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Command;

use LaDanse\ServicesBundle\Common\CommandExecutionContext;
use LaDanse\ServicesBundle\Service\DTO\Character\Character;
use LaDanse\ServicesBundle\Service\DTO\Character\PatchCharacter;
use LaDanse\ServicesBundle\Service\DTO\Character\PatchClaim;
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
class FixRaiderMaxLevelCommand extends ContainerAwareCommand
{
    /** @var int */
    const MAX_LEVEL = 60;

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('ladanse:fixRaiderMaxLevel')
            ->setDescription('Remove raider mark from characters that are not max level')
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

        $this->fixRaiderMaxLevel($context);
    }

    private function fixRaiderMaxLevel(CommandExecutionContext $context)
    {
        /** @var CharacterService $characterService */
        $characterService = $this->getContainer()->get(CharacterService::SERVICE_NAME);

        $allClaimedCharacters = $characterService->getAllClaimedCharacters(new \DateTime());

        foreach ($allClaimedCharacters as $claimedCharacter)
        {
            /** @var Character $claimedCharacter */

            if ($claimedCharacter->getLevel() !== self::MAX_LEVEL && $claimedCharacter->getClaim()->isRaider())
            {
                $context->info("Character "
                    . $claimedCharacter->getName()
                    . " is marked as raider but is only level "
                    . $claimedCharacter->getLevel()
                    . ", fixing");

                $patchClaim = new PatchClaim();

                $patchClaim->setRaider(false);
                $patchClaim->setComment($claimedCharacter->getClaim()->getComment());
                $patchClaim->setRoles($claimedCharacter->getClaim()->getRoles());

                $characterService->putClaim($claimedCharacter->getId(), $patchClaim);
            }
        }
    }
}

