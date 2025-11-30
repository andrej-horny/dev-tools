<?php

namespace DevTools\Commands\Domain;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MakeEntityServiceCommand extends BaseGeneratorCommand
{
    protected static $defaultName = 'make:domain:entity-service';

    protected function configure(): void
    {
        $this
            ->setDescription('Generate a create and update services for an entity')
            ->addOption('entity-name', null, InputOption::VALUE_REQUIRED, 'Entity name')
            ->addOption('pkg-namespace', null, InputOption::VALUE_OPTIONAL, 'Domain package namespace')
            ->addOption('domain-name', null, InputOption::VALUE_OPTIONAL, 'Domain name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $entityName = $input->getOption('entity-name');
        $packageNamespace = $input->getOption('pkg-namespace');
        $domainName = $input->getOption('domain-name');

        $template = $this->getTemplate('entity_service');

        // build namespace
        $namespace = $packageNamespace;
        $namespace .= ($domainName !== null) ? "\\Domain\\" . $domainName : ""; 
        $namespace .= "\\Services";

        // build entityClassPath
        $entityClassPath = $packageNamespace;
        $entityClassPath .= ($domainName !== null) ? "\\Domain\\" . $domainName : ""; 
        $entityClassPath .= "\\Entities\\$entityName";

        // build repositoryClassPath
        $repositoryClassPath = $packageNamespace;
        $repositoryClassPath .= ($domainName !== null) ? "\\Domain\\" . $domainName : ""; 
        $repositoryClassPath .= "\\Repositories\\{$entityName}RepositoryInterface";

        $content = str_replace(
            [
                '{{ namespace }}',
                '{{ entityClassPath }}',
                '{{ repositoryClassPath }}',
                '{{ entityName }}'
            ],
            [
                $namespace,
                $entityClassPath,
                $repositoryClassPath,
                $entityName
            ],

            $template
        );

        $dir = 'src';
        $dir .= ($domainName !== null) ? '/Domain/' . $domainName : "";
        $dir .= '/Services';
        $this->ensureDirectory($dir);

        // create entity service
        $createServiceFile = "{$dir}/Create{$entityName}Service.php";
        file_put_contents($createServiceFile, $content);
        $output->writeln("<info>Create {$entityName} service generated!</info>");

        // update entity service
        $updateServiceFile = "{$dir}/Update{$entityName}Service.php";
        file_put_contents($updateServiceFile, $content);
        $output->writeln("<info>Update {$entityName} service generated!</info>");

        return self::SUCCESS;
    }
}
