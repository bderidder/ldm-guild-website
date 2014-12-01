<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Command;

use \Symfony\Component\Console\Input\InputArgument;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Input\InputOption;
use \Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CommandContext
 * @package LaDanse\ServicesBundle\Command
 */
class CommandExecutionContext
{
    private $input;
    private $output;

    function _construct(InputInterface $input, OutputInterface $output)
    {

    }
} 