<?php

namespace DevTools\Commands\Infrastructure;

use DevTools\Traits\FileGenerator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'make:infrastructure:cqrs-handler',
    description: 'Generate CQRS command for entity.',
)]
class MakeCQRSHandlerCommand extends Command
{
    use FileGenerator;

    protected array $config;
    
    public function __construct()
    {
        parent::__construct();
        $this->config = require __DIR__ . '/../../../config/infrastructure.php';
    }

    protected function configure(): void
    {
        $this
            ->addOption('entity-name', null, InputOption::VALUE_REQUIRED, 'Entity name')
            ->addOption('handler-name', null, InputOption::VALUE_REQUIRED, 'Command name')
            ->addOption('pkg-namespace', null, InputOption::VALUE_OPTIONAL, 'Infrastructure package namespace');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $entityName = $input->getOption('entity-name');
        $handlerName = $input->getOption('handler-name');
        $packageNamespace = $input->getOption('pkg-namespace');

        // get template
        $template = $this->getTemplate('cqrs_handler');

        // build namespace
        $namespace = $packageNamespace;
        $namespace .= "\\{$this->config['default_namespaces']['cqrs_handler']}";
        $namespace .= "\\{$entityName}";

        // build entityClassPath
        $commandClassPath = $packageNamespace;
        $commandClassPath .= "\\{$this->config['default_namespaces']['cqrs_command']}";
        $commandClassPath .= "\\{$entityName}\\{$handlerName}Command";

        // build modelClassPath
        $modelClassPath = $packageNamespace;
        $modelClassPath .= "\\Models\\{$entityName}";

        $content = str_replace(
            [
                '{{ namespace }}',
                '{{ entityName }}',
                '{{ modelClassPath }}',
                '{{ commandClassPath }}',
                '{{ handlerName }}',
            ],
            [
                $namespace,
                $entityName,
                $modelClassPath,
                $commandClassPath,
                $handlerName,
            ],
            $template
        );

        // build dir path
        $dir = "{$this->config['default_paths']['cqrs_handler']}";
        $dir .= "/{$entityName}";
        $this->ensureDirectory($dir);

        $file = "{$dir}/{$handlerName}Handler.php";
        file_put_contents($file, $content);

        $output->writeln("<info>CQRSHandler {$handlerName}Handler generated!</info>");

        return Command::SUCCESS;
    }
}
