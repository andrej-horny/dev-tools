<?php

namespace DevTools\Commands\Infrastructure;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MakeCQRSCommandCommand extends BaseGeneratorCommand
{
    protected static $defaultName = 'make:infrastructure:cqrs-command';

    protected function configure(): void
    {
        $this
            ->setDescription('Generate CQRS command for entity')
            ->addOption('entity-name', null, InputOption::VALUE_REQUIRED, 'Entity name')
            ->addOption('command-name', null, InputOption::VALUE_REQUIRED, 'Command name')
            ->addOption('pkg-namespace', null, InputOption::VALUE_OPTIONAL, 'Infrastructure package namespace')
            ->addOption('props', null, InputOption::VALUE_OPTIONAL, 'Comma separated props')
            ->addOption('with-handler', null, InputOption::VALUE_NONE, 'Also generate the handler for this command');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $application = $this->getApplication();
        $entityName = $input->getOption('entity-name');
        $commandName = $input->getOption('command-name');
        $packageNamespace = $input->getOption('pkg-namespace');
        $propsArg = $input->getOption('props') ?? '';

        // get template
        $template = $this->getTemplate('cqrs_command');

        // build namespace
        $namespace = $packageNamespace;
        $namespace .= "\\{$this->config['default_namespaces']['cqrs_command']}";
        $namespace .= "\\{$entityName}";

        // build props
        $props = [];

        foreach (array_filter(array_map('trim', explode(',', $propsArg))) as $prop) {
            if (preg_match('/^([\w\\\\|?]+)\s+\$(\w+)$/', $prop, $matches)) {
                $props[] = ['visibility' => 'public', 'type' => $matches[1], 'name' => $matches[2]];
            }
        }

        // constructor params
        $constructorParams = $this->parseConstructorParams($props);
        $constructorParams = rtrim($constructorParams, ",\n");

        $content = str_replace(
            [
                '{{ namespace }}', 
                '{{ entityName }}', 
                '{{ commandName }}', 
                '{{ constructorParams }}', 
            ],
            [
                $namespace, 
                $entityName, 
                $commandName, 
                $constructorParams,
            ],
            $template
        );

        // build dir path
        $dir = "{$this->config['default_paths']['cqrs_command']}";
        $dir .= "/{$entityName}";
        $this->ensureDirectory($dir);

        $file = "{$dir}/{$commandName}Command.php";
        file_put_contents($file, $content);

        $output->writeln("<info>CQRSCommand {$commandName}Command generated!</info>");

        // Optionally generate handler
        if ($input->getOption('with-handler')) {
            $repoInput = new ArrayInput([
                'command' => 'make:infrastructure:handler',
                '--entity-name' => $entityName,
                '--pkg-namespace' => $packageNamespace,
                '--handler-name' => $commandName,
            ]);
            $application->find('make:infrastructure:cqrs-handler')->run($repoInput, $output);
        }

        return self::SUCCESS;
    }
}
