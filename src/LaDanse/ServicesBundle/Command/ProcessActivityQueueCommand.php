<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Command;

use LaDanse\DomainBundle\Entity\ActivityQueueItem;
use LaDanse\ServicesBundle\Notification\NotificationService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ProcessActivityQueueCommand
 * @package LaDanse\ServicesBundle\Command
 */
class ProcessActivityQueueCommand extends ContainerAwareCommand
{
    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('ladanse:processActivityQueue')
            ->setDescription('Process all outstanding activities')
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

        /* @var $notificationSvc NotificationService */
        $notificationSvc = $this->getContainer()->get(NotificationService::SERVICE_NAME);

        $em = $this->getContainer()->get('doctrine')->getManager();

        /* @var $queueRepo \Doctrine\ORM\EntityRepository */
        $queueRepo = $em->getRepository(ActivityQueueItem::REPOSITORY);

        /* @var $items array */
        $items = $queueRepo->findAll();

        $context->info("Found items: " . count($items));

        /* @var $item ActivityQueueItem */
        foreach($items as $item)
        {
            if ($notificationSvc->hasNotificationsFor($item->getActivityType()))
            {
                $context->info(
                    $item->getActivityType()
                    . " by "
                    . $item->getActivityBy()->getDisplayName()
                    . " on " . $item->getActivityOn()->format("d/m/Y h:i:s")
                );

                $notificationSvc->processForNotification($item);
            }
        }
    }
}