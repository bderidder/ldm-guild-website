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
use LaDanse\DomainBundle\Entity\GameData\Guild;
use LaDanse\DomainBundle\Entity\GameData\Realm;
use LaDanse\ServicesBundle\Common\CommandExecutionContext;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RefreshGuildMembersCommand
 * @package LaDanse\ServicesBundle\Command
 */
class FetchGameDataIdsCommand extends ContainerAwareCommand
{
    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('ladanse:fetchGameDataIds')
            ->setDescription('Fetch game ids for guilds and realms');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     *
     * @throws ConnectionException
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->doctrine = $this->getContainer()->get('doctrine');
        $this->entityManager = $this->doctrine->getManager();

        $context = new CommandExecutionContext(
            $input,
            $output
        );

        $this->entityManager->getConnection()->beginTransaction();
        try
        {
            $accessToken = BattleNetUtils::getBlizzardAccessToken(
                $context,
                $this->getContainer()->getParameter("battlenet_key"),
                $this->getContainer()->getParameter("battlenet_secret"));

            $this->fetchGameIdsForRealms($context, $accessToken);
            $this->fetchGameIdsForGuilds($context, $accessToken);

            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();
        }
        catch (Exception $e)
        {
            $this->entityManager->getConnection()->rollBack();

            throw $e;
        }
    }

    private function fetchGameIdsForRealms(CommandExecutionContext $context, string $accessToken): void
    {
        /** @var array $realms */
        $realms = $this->entityManager->getRepository(Realm::REPOSITORY)->findAll();

        foreach($realms as $realm)
        {
            /** @var Realm $realm */

            $context->info('Fetching BattleNet data for realm ' . $realm->getName());

            $endpointUrl = "https://eu.api.blizzard.com/data/wow/realm/" . BattleNetUtils::sluggify($realm->getName());

            try
            {
                $response = BattleNetUtils::callBattleNetAPI($context, $accessToken, 'dynamic', $endpointUrl);

                $context->debug(\GuzzleHttp\json_encode($response));

                $realm->setGameId($response->id);
                $this->entityManager->flush();
            }
            catch (Exception $e)
            {
                $context->info('Could not fetch realm '
                    . $realm->getName()
                    . ': '
                    . $e->getMessage());
            }
        }
    }

    private function fetchGameIdsForGuilds(CommandExecutionContext $context, string $accessToken): void
    {
        /** @var array $guilds */
        $guilds = $this->entityManager->getRepository(Guild::REPOSITORY)->findAll();

        foreach($guilds as $guild)
        {
            /** @var Guild $guild */

            /** @var Realm $realm */
            $realm = $guild->getRealm();

            $context->debug('Fetching BattleNet data for guild '
                . $guild->getName()
                . ' on realm '
                . $realm->getName());

            $endpointUrl = "https://eu.api.blizzard.com/data/wow/guild/"
                . BattleNetUtils::sluggify($realm->getName())
                . '/'
                . BattleNetUtils::sluggify($guild->getName());

            try
            {
                $response = BattleNetUtils::callBattleNetAPI($context, $accessToken, 'profile', $endpointUrl);

                $context->debug(\GuzzleHttp\json_encode($response));

                $guild->setGameId($response->id);
                $this->entityManager->flush();
            }
            catch (Exception $e)
            {
                $context->info('Could not fetch realm '
                    . $realm->getName()
                    . ': '
                    . $e->getMessage());
            }
        }
    }
}

