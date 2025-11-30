<?php

namespace DevTools\Commands\Domain;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MakeValueObjectIdCommand extends BaseGeneratorCommand
{
    protected static $defaultName = 'make:domain:value-object-id';

    protected function configure()
    {
        $this
            ->setDescription('Generate a DDD ValueObjectId class')
            ->addOption('entity-name', null, InputOption::VALUE_REQUIRED, 'Entity name')
            ->addOption('pkg-namespace', null, InputOption::VALUE_OPTIONAL, 'Domain package namespace')
            ->addOption('domain-name', null, InputOption::VALUE_OPTIONAL, 'Domain name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entityName = $input->getOption('entity-name');
        $packageNamespace = $input->getOption('pkg-namespace');
        $domainName = $input->getOption('domain-name') ?? null;

        // build namespace
        $namespace = $packageNamespace;
        $namespace .= ($domainName !== null) ? "\\Domain\\" . $domainName : ""; 
        $namespace .= "\\Entities";

        $template = $this->getTemplate('value_object_id');

        $content = str_replace(
            [
                '{{ namespace }}',
                '{{ entityName }}',
            ],
            [
                $namespace,
                $entityName,
            ],
            $template
        );

        // $dir = $this->config['default_paths']['value_object_id'] . "\\$domainName";
        $dir = 'src';
        $dir .= ($domainName !== null) ? '/Domain/' . $domainName : "";
        $dir .= '/Entities';
        $this->ensureDirectory($dir);

        $file = "{$dir}/{$entityName}Id.php";
        file_put_contents($file, $content);

        $output->writeln("<info>Value object Id {$entityName}Id generated!</info>");

        return self::SUCCESS;
    }
}
