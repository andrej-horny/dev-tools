<?php

namespace DevTools\Commands\Infrastructure;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MakeModelCommand extends BaseGeneratorCommand
{
    protected static $defaultName = 'make:infrastructure:model';

    protected function configure(): void
    {
        $this
            ->setDescription('Generate eloquent model for domain entity aggregate')
            ->addOption('entity-name', null, InputOption::VALUE_REQUIRED, 'Entity name')
            ->addOption('pkg-namespace', null, InputOption::VALUE_OPTIONAL, 'Infrastructure package namespace')
            ->addOption('domain-name', null, InputOption::VALUE_OPTIONAL, 'Domain name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $entityName = $input->getOption('entity-name');
        $domainName = $input->getOption('domain-name');
        $packageNamespace = $input->getOption('pkg-namespace');

        // get template
        $template = $this->getTemplate('model');

        // build namespace
        $namespace = $packageNamespace;
        $namespace .= "\\{$this->config['default_namespaces']['model']}";
        $namespace .= "\\{$domainName}";

        // build ulidCastClassPath
        $ulidCastClassPath = $this->config['default_namespaces']['ulid_cast'];
        $ulidTraitClassPath = $this->config['default_namespaces']['ulid_trait'];
        
        $content = str_replace(
            [
                '{{ namespace }}', 
                '{{ ulidCastClassPath }}', 
                '{{ ulidTraitClassPath }}', 
                '{{ entityName }}', 
            ],
            [
                $namespace, 
                $ulidCastClassPath, 
                $ulidTraitClassPath, 
                $entityName, 
            ],
            $template
        );

        // build dir path
        $dir = "{$this->config['default_paths']['model']}";
        $dir .= "/{$domainName}";
        $this->ensureDirectory($dir);

        $file = "{$dir}/Eloquent{$entityName}.php";
        file_put_contents($file, $content);

        $output->writeln("<info>Eloquent model Eloquent{$entityName} generated!</info>");

        return self::SUCCESS;
    }
}
