<?php
/**
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://github.com/bderidder/ldm-guild-website
 */

namespace LaDanse\ServicesBundle\Common;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CommandContext
 * @package LaDanse\ServicesBundle\Command
 */
class CommandExecutionContext
{
    /** @var $input InputInterface */
    private $input;
    /** @var $output OutputInterface */
    private $output;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * @return InputInterface
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param $text
     */
    public function debug($text)
    {
        if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE)
        {
            $this->output->writeln($text);
        }
    }

    /**
     * @param $text
     */
    public function error($text)
    {
        $this->output->writeln($text);
    }

    /**
     * @param $text
     */
    public function info($text)
    {
        if ($this->output->getVerbosity() == OutputInterface::VERBOSITY_VERBOSE)
        {
            $this->output->writeln($text);
        }
    }
} 