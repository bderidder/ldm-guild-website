<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Command;

use JMS\Serializer\SerializerBuilder;
use LaDanse\DomainBundle\Entity as Entity;
use LaDanse\ServicesBundle\Service\DTO as DTO;
use \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Output\OutputInterface;


/**
 * Class ExportDataCommand
 * @package LaDanse\ServicesBundle\Command
 */
class ExportDataCommand extends ContainerAwareCommand
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
            ->setName('ladanse:data:export')
            ->setDescription('Export all data in JSON aggregates')
            ->addArgument(
                'destination',
                InputArgument::REQUIRED,
                'Where do want the JSON exports to be saved?'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $destination = $input->getArgument('destination');

        if (!file_exists($destination) || is_file($destination))
        {
            $output->writeln('ERROR - The given destination is not a directory');

            return;
        }

        /** @var \Doctrine\Bundle\DoctrineBundle\Registry $doctrine */
        $doctrine = $this->getContainer()->get('doctrine');

        /* @var $repository \Doctrine\ORM\EntityRepository */
        $repository = $doctrine->getRepository(Entity\Event::REPOSITORY);

        $events = $repository->findAll();

        $jsonEvents = array();

        /** @var Entity\Event $event */
        foreach($events as $event)
        {
            $jsonEvents[] = DTO\Event\EventAggregateFactory::create($event);
        }

        /** @var DTO\Event\Event $jsonEvent */
        foreach($jsonEvents as $jsonEvent)
        {
            $serializer = SerializerBuilder::create()->build();
            $jsonContent = $serializer->serialize($jsonEvent, 'json');

            $exportFilename = $destination . "/event_" . $jsonEvent->getId() . ".json";

            $exportFile = fopen($exportFilename, "w") or die("Unable to open file!");

            fwrite($exportFile, $jsonContent);

            fclose($exportFile);
        }
    }
}