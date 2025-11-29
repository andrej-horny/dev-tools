<?php

namespace DevTools\Commands\Infrastructure;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MakeRepositoryCommand extends Command
{
    protected array $config;

    protected static $defaultName = 'make:infrastructure:repository';

    public function __construct()
    {
        parent::__construct();
        $this->config = require __DIR__ . '/../../../config/infrastructure.php';
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Generate a repository implementation repository interface')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'Entity name')
            ->addOption('pkg-namespace', 'pkg-ns', InputOption::VALUE_OPTIONAL, 'Entity namespace')
            ->addOption('domain-name', 'd', InputOption::VALUE_OPTIONAL, 'Domain name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $entityName = $input->getOption('name');
        $packageNamespace = $input->getOption('pkg-namespace') ?: $this->config['default_namespaces']['entity'];
        $domainName = $input->getOption('domain-name');

        $templatePath = $this->config['templates']['repository'] ?? null;
        if (!$templatePath || !file_exists($templatePath)) {
            throw new \RuntimeException("Template not found for repository at {$templatePath}");
        }

        $template = file_get_contents($templatePath);

        $content = str_replace(
            [
                '{{ packageNamespace }}', 
                '{{ repositoryNamespace }}', 
                '{{ mapperNamespace }}', 
                '{{ modelNamespace }}', 
                '{{ domainName }}', 
                '{{ entityName }}', 
            ],
            [
                $packageNamespace, 
                $this->config['default_namespaces']['repository'], 
                $this->config['default_namespaces']['mapper'], 
                $this->config['default_namespaces']['model'], 
                $domainName, 
                $entityName, 
            ],
            $template
        );

        $dir = $this->config['default_paths']['repository'] ?? 'src';
        $this->ensureDirectory($dir);

        $file = "{$dir}/{$entityName}RepositoryEloquent.php";
        file_put_contents($file, $content);

        $output->writeln("<info>Repository implementation {$entityName}RepositoryEloquent generated!</info>");

        return self::SUCCESS;
    }

    private function ensureDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }
}
