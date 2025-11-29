<?php

namespace DevTools\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MakeRepositoryCommand extends Command
{
    protected array $config;

    protected static $defaultName = 'make:repository';

    public function __construct()
    {
        parent::__construct();
        $this->config = require __DIR__ . '/../../config.php';
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Generate a repository interface for an entity')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'Entity name')
            ->addOption('entity-namespace', null, InputOption::VALUE_OPTIONAL, 'Entity namespace', 'App\\Domain')
            ->addOption('repository-namespace', null, InputOption::VALUE_OPTIONAL, 'Repository namespace', 'App\\Domain\\Repository')
            ->addOption('base-repository', null, InputOption::VALUE_OPTIONAL, 'Base repository interface', 'App\\Domain\\Repository\\RepositoryInterface');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $entityName = $input->getOption('name');
        $entityNamespace = $input->getOption('entity-namespace') ?: $this->config['default_namespaces']['entity'] . "\\$entityName";
        $repositoryNamespace = $input->getOption('repository-namespace') ?: $this->config['default_namespaces']['repository'] . "\\$entityName";
        $baseRepository = $input->getOption('base-repository') ?: $this->config['default_namespaces']['repository'] . "\\$entityName\\RepositoryInterface";

        $templatePath = $this->config['templates']['entity_repository'] ?? null;
        if (!$templatePath || !file_exists($templatePath)) {
            throw new \RuntimeException("Template not found for entity_repository at {$templatePath}");
        }

        $template = file_get_contents($templatePath);

        $content = str_replace(
            ['{{ repositoryNamespace }}', '{{ entityNamespace }}', '{{ entityName }}', '{{ baseRepositoryInterface }}', '{{ entityName }}Id'],
            [$repositoryNamespace, $entityNamespace, $entityName, $baseRepository, $entityName . 'Id'],
            $template
        );

        $defaultDir = $this->config['default_paths']['entity_repository'] ?? 'src';
        $namespaceDir = str_replace('\\', '/', $repositoryNamespace);
        $dir = !empty($repositoryNamespace) ? $namespaceDir : $defaultDir;
        $this->ensureDirectory($dir);

$file = "{$dir}/{$entityName}RepositoryInterface.php";
        file_put_contents($file, $content);

        $output->writeln("<info>Repository interface {$entityName}Repository generated!</info>");

        return self::SUCCESS;
    }

    private function ensureDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }
}
