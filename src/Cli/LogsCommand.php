<?php

namespace Dock\Cli;

use Dock\Compose\ComposeExecutableFinder;
use Dock\IO\ProcessRunner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LogsCommand extends Command
{
    /**
     * @var ComposeExecutableFinder
     */
    private $composeExecutableFinder;

    /**
     * @param ComposeExecutableFinder $composeExecutableFinder
     */
    public function __construct(ComposeExecutableFinder $composeExecutableFinder)
    {
        parent::__construct();

        $this->composeExecutableFinder = $composeExecutableFinder;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('logs')
            ->setDescription('Follow logs of application containers')
            ->addArgument('component', InputArgument::OPTIONAL, 'Name of component to follow')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $composeLogsArguments = ['logs'];
        if (null !== ($component = $input->getArgument('component'))) {
            $composeLogsArguments[] = $component;
        }

        pcntl_exec($this->composeExecutableFinder->find(), $composeLogsArguments);
    }
}
