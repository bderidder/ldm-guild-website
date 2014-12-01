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
    /** @var $input InputInterface */
    private $input;
    /** @var $output OutputInterface */
    private $output;

    private $isVerbose;
    private $isDiag;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function __construct(InputInterface $input, OutputInterface $output, $isVerbose, $isDiag)
    {
        $this->input = $input;
        $this->output = $output;

        $this->isVerbose = $isVerbose;
        $this->isDiag = $isDiag;
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
        if ($this->isDiag)
        {
            $this->output->writeln($text);
        }
    }

    /**
     * @param $text
     */
    public function info($text)
    {
        if ($this->isVerbose or $this->isDiag)
        {
            $this->output->writeln($text);
        }
    }
} 