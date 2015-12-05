<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ForumBundle\Command;

use LaDanse\ForumBundle\Service\ForumService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UpdateLastPostCommand
 * @package LaDanse\ForumBundle\Command
 */
class UpdateLastPostCommand extends ContainerAwareCommand
{
    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('ladanse:updateForumsLastPost')
            ->setDescription('Update the last post data in Forums and Topics')
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
        /** @var ForumService $forumService */
        $forumService = $this->getContainer()->get(ForumService::SERVICE_NAME);

        $forumService->updateLastPosts();
    }
}