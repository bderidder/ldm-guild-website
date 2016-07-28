<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ProcessActivityQueueCommand
 * @package LaDanse\ServicesBundle\Command
 */
class ActivityStreamQueueCommand extends ContainerAwareCommand
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
            ->setName('ladanse:queue:activityStream')
            ->setDescription('Process activity stream items on the queue given the command')
            ->addOption(
                NotificationQueueCommand::OPTION_PURGE,
                null,
                InputOption::VALUE_NONE,
                'If set, all processed activity stream items are purged from the queue'
            )
            ->addOption(
                NotificationQueueCommand::OPTION_PROCESS,
                null,
                InputOption::VALUE_NONE,
                'If set, process all activity stream items that have not been processed successfully so far'
            )
            ->addOption(
                NotificationQueueCommand::OPTION_LIST,
                null,
                InputOption::VALUE_NONE,
                'If set, list all the activity stream items that have not been processed successfully so far'
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
        if ($input->getOption(ActivityStreamQueueCommand::OPTION_PROCESS))
        {

        }
        else if ($input->getOption(ActivityStreamQueueCommand::OPTION_PURGE))
        {

        }
        else if ($input->getOption(ActivityStreamQueueCommand::OPTION_LIST))
        {

        }
    }
}