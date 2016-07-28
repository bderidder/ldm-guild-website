<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Command;

use LaDanse\ServicesBundle\Service\Event\EventService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UpdateLastPostCommand
 * @package LaDanse\ForumBundle\Command
 */
class EventTodayCommand extends ContainerAwareCommand
{
    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('ladanse:notifyEventToday')
            ->setDescription('Notify people for events that happen today')
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
        /** @var EventService $eventService */
        $eventService = $this->getContainer()->get(EventService::SERVICE_NAME);

        $eventService->notifyEventsToday();
    }
}