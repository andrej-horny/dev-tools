<?php

namespace DevTools\Commands\Infrastructure;

use DevTools\Traits\FileGenerator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'cmd:test',
    description: 'test command',
)]
// class MakeCQRSCommandCommand extends BaseGeneratorCommand
class TestCommand extends Command
{
    use FileGenerator;

    protected array $config;
    
    // public function __construct()
    // {
    //     parent::__construct('make:infrastructure:cqrs-command');
    // }
    public function __construct()
    {
        parent::__construct();
        $this->config = require __DIR__ . '/../../../config/infrastructure.php';
    }

    protected function configure(): void
    {
        $this
            ->addOption('entity-name', null, InputOption::VALUE_REQUIRED, 'Entity name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $application = $this->getApplication();
        $entityName = $input->getOption('entity-name');
    $output->writeln("test {$entityName}");

        return Command::SUCCESS;
    }
}
