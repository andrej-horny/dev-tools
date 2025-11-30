<?php

namespace DevTools\Commands\Infrastructure;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MakeMapperCommand extends Command
{
    protected array $config;

    protected static $defaultName = 'make:infrastructure:mapper';

    public function __construct()
    {
        parent::__construct();
        $this->config = require __DIR__ . '/../../../config/infrastructure.php';
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Generate a mapepr between infrastructure model and domain entity')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'Entity name')
            ->addOption('ipkg-namespace', null, InputOption::VALUE_OPTIONAL, 'Infrastructure package namespace')
            ->addOption('domain-name', null, InputOption::VALUE_OPTIONAL, 'Domain name')
            ->addOption('dpkg-namespace', null, InputOption::VALUE_OPTIONAL, 'Domain package namespace');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $entityName = $input->getOption('name');
        $domainName = $input->getOption('domain-name');
        $infrastructurePkgNamespace = $input->getOption('ipkg-namespace');
        $domainPkgNamespace = $input->getOption('dpkg-namespace') ?: $infrastructurePkgNamespace;

        $templatePath = $this->config['templates']['mapper'] ?? null;
        if (!$templatePath || !file_exists($templatePath)) {
            throw new \RuntimeException("Template not found for mapper at {$templatePath}");
        }

        $template = file_get_contents($templatePath);

        $content = str_replace(
            [
                '{{ infrastructurePkgNamespace }}', 
                '{{ mapperNamespace }}', 
                '{{ modelNamespace }}', 
                '{{ domainPkgNamespace }}', 
                '{{ domainName }}', 
                '{{ entityName }}', 
            ],
            [
                $infrastructurePkgNamespace, 
                $this->config['default_namespaces']['mapper'], 
                $this->config['default_namespaces']['model'], 
                $domainPkgNamespace,
                $domainName, 
                $entityName, 
            ],
            $template
        );

        $dir = $this->config['default_paths']['mapper'] ?? 'src';
        $this->ensureDirectory($dir);

        $file = "{$dir}/{$entityName}Mapper.php";
        file_put_contents($file, $content);

        $output->writeln("<info>Mapepr {$entityName}Mapper generated!</info>");

        return self::SUCCESS;
    }

    private function ensureDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }
}
