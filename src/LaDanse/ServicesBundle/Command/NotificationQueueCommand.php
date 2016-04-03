<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Command;

use \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Output\OutputInterface;
use LaDanse\ServicesBundle\Notification\NotificationQueue;

use Symfony\Component\Console\Input\InputOption;

/**
 * Class NotificationQueueCleanCommand
 * @package LaDanse\ServicesBundle\Command
 */
class NotificationQueueCommand extends ContainerAwareCommand
{
    const OPTION_PURGE   = 'purge';
    const OPTION_PROCESS = 'process';
    const OPTION_LIST    = 'list';

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('ladanse:queue:notifications')
            ->setDescription('Process notification items on the queue given the command')
            ->addOption(
                NotificationQueueCommand::OPTION_PURGE,
                null,
                InputOption::VALUE_NONE,
                'If set, all processed notification items are purged from the queue'
            )
            ->addOption(
                NotificationQueueCommand::OPTION_PROCESS,
                null,
                InputOption::VALUE_NONE,
                'If set, process all notifications items that have not been processed successfully so far'
            )
            ->addOption(
                NotificationQueueCommand::OPTION_LIST,
                null,
                InputOption::VALUE_NONE,
                'If set, list all the notifications items that have not been processed successfully so far'
            )
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
        /* @var NotificationQueue $notificationQueue */
        $notificationQueue = $this->getContainer()->get(NotificationQueue::SERVICE_NAME);

        if ($input->getOption(NotificationQueueCommand::OPTION_PROCESS))
        {
            $notificationQueue->processQueue();
        }
        else if ($input->getOption(NotificationQueueCommand::OPTION_PURGE))
        {
            $notificationQueue->cleanQueue();
        }
        else if ($input->getOption(NotificationQueueCommand::OPTION_LIST))
        {
            $notificationQueue->listQueue($output);
        }
    }
}