<?php

namespace DevTools\Commands\Infrastructure;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MakeRepositoryCommand extends BaseGeneratorCommand
{
    protected static $defaultName = 'make:infrastructure:repository';

    protected function configure(): void
    {
        $this
            ->setDescription('Generate a repository implementation repository interface')
            ->addOption('entity-name', null, InputOption::VALUE_REQUIRED, 'Entity name')
            ->addOption('ipkg-namespace', null, InputOption::VALUE_OPTIONAL, 'Infrastructure package namespace')
            ->addOption('domain-name', null, InputOption::VALUE_OPTIONAL, 'Domain name')
            ->addOption('dpkg-namespace', null, InputOption::VALUE_OPTIONAL, 'Domain package namespace');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $entityName = $input->getOption('entity-name');
        $domainName = $input->getOption('domain-name');
        $infrastructurePkgNamespace = $input->getOption('ipkg-namespace');
        $domainPkgNamespace = $input->getOption('dpkg-namespace') ?: $infrastructurePkgNamespace;

        // get template
        $template = $this->getTemplate('repository');

        // build namespace
        $namespace = $infrastructurePkgNamespace;
        $namespace .= "\\{$this->config['default_namespaces']['mapper']}";
        $namespace .= "\\{$domainName}";

        // build entityClassPath
        $entityClassPath = $domainPkgNamespace;
        $entityClassPath .= ($domainName !== null) ? "\\Domain\\" . $domainName : ""; 
        $entityClassPath .= "\\Entities\\$entityName";

        // build repositoryInterfacePath
        $repositoryInterfacePath = $domainPkgNamespace;
        $repositoryInterfacePath .= ($domainName !== null) ? "\\Domain\\" . $domainName : ""; 
        $repositoryInterfacePath .= "\\Repositories\\{$entityName}RepositoryInterface";

        // build mapperClassPath
        $mapperClassPath = $infrastructurePkgNamespace;
        $mapperClassPath .= "\\{$this->config['default_namespaces']['mapper']}";
        $mapperClassPath .= "\\{$domainName}Mapper";

        // build modelClassPath
        $modelClassPath = $infrastructurePkgNamespace;
        $modelClassPath .= "\\{$this->config['default_namespaces']['model']}";
        $modelClassPath .= "\\Eloquent{$domainName}";
        
        $content = str_replace(
            [
                '{{ namespace }}', 
                '{{ entityClassPath }}', 
                '{{ repositoryInterfacePath }}', 
                '{{ mapperClassPath }}', 
                '{{ modelClassPath }}', 
                '{{ entityName }}', 
            ],
            [
                $namespace, 
                $entityClassPath, 
                $repositoryInterfacePath, 
                $mapperClassPath, 
                $modelClassPath, 
                $entityName, 
            ],
            $template
        );

        // build dir path
        $dir = "{$this->config['default_paths']['repository']}";
        $dir .= "/{$domainName}";
        $this->ensureDirectory($dir);

        $file = "{$dir}/{$entityName}RepositoryEloquent.php";
        file_put_contents($file, $content);

        $output->writeln("<info>Repository implementation {$entityName}RepositoryEloquent generated!</info>");

        return self::SUCCESS;
    }
}
