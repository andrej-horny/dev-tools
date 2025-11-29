<?php

namespace DevTools\Commands\Domain;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MakeEntityServiceCommand extends Command
{
    protected array $config;

    protected static $defaultName = 'make:entity-service';

    public function __construct()
    {
        parent::__construct();
        $this->config = require __DIR__ . '/../../../config.php';
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Generate a create and update services for an entity')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'Entity name')
            ->addOption('entity-namespace', null, InputOption::VALUE_OPTIONAL, 'Entity namespace')
            ->addOption('repository-namespace', null, InputOption::VALUE_OPTIONAL, 'Repository namespace')
            ->addOption('service-namespace', null, InputOption::VALUE_OPTIONAL, 'Service namespace');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $entityName = $input->getOption('name');
        $entityNamespace = $input->getOption('entity-namespace') ?: $this->config['default_namespaces']['entity'];
        $repositoryNamespace = $input->getOption('repository-namespace') ?: $this->config['default_namespaces']['entity_repository'];
        $serviceNamespace = $input->getOption('service-namespace') ?: $this->config['default_namespaces']['entity_service'];

        $templatePath = $this->config['templates']['entity_service'] ?? null;
        if (!$templatePath || !file_exists($templatePath)) {
            throw new \RuntimeException("Template not found for entity_service at {$templatePath}");
        }

        $template = file_get_contents($templatePath);

        $content = str_replace(
            ['{{ serviceNamespace }}', '{{ repositoryNamespace }}', '{{ entityNamespace }}', '{{ entityName }}'],
            [$serviceNamespace, $repositoryNamespace, $entityNamespace, $entityName],
            $template
        );

        // $defaultDir = $this->config['default_paths']['entity_service'] ?? 'src';
        // $namespaceDir = str_replace('\\', '/', $serviceNamespace);
        // $dir = !empty($serviceNamespace) ? $namespaceDir : $defaultDir;
        $dir = $this->config['default_paths']['entity_service'] ?? 'src';
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

    private function ensureDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }
}
