<?php

namespace DevTools\Commands\Domain;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MakeEntityCommand extends BaseGeneratorCommand
{
    protected static $defaultName = 'make:domain:entity';

    protected function configure()
    {
        $this
            ->setDescription('Generate a DDD Entity class')
            ->addOption('entity-name', null, InputOption::VALUE_REQUIRED, 'Entity name')
            ->addOption('pkg-namespace', null, InputOption::VALUE_OPTIONAL, 'Domain package namespace')
            ->addOption('domain-name', null, InputOption::VALUE_OPTIONAL, 'Domain name')
            ->addOption('props', null, InputOption::VALUE_OPTIONAL, 'Comma separated props')
            // ->addOption('with-id', null, InputOption::VALUE_NONE, 'Also generate the entity ID value object')
            ->addOption('with-repo', null, InputOption::VALUE_NONE, 'Also generate the repository for this entity')
            ->addOption('with-svc', null, InputOption::VALUE_NONE, 'Also generate the services for this entity');
        // ->addOption('with-vo', null, InputOption::VALUE_NONE, 'Also generate property value objects');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $application = $this->getApplication();
        $entityName = $input->getOption('entity-name');
        $packageNamespace = $input->getOption('pkg-namespace') ?: $this->config['default_namespaces']['domain'];
        $domainName = $input->getOption('domain-name');
        $propsArg = $input->getOption('props') ?? '';
        $props = [];

        foreach (array_filter(array_map('trim', explode(',', $propsArg))) as $prop) {
            if (preg_match('/^([\w\\\\|?]+)\s+\$(\w+)$/', $prop, $matches)) {
                $props[] = ['type' => $matches[1], 'name' => $matches[2]];
            }
        }

        // constructor params
        $constructorParams = $this->parseConstructorParams($props);
        $constructorParams = rtrim($constructorParams, ",\n");

        // accessors
        $accessors = $this->parseAccessors($props);

        // build namespace
        $namespace = $packageNamespace;
        $namespace .= ($domainName !== null) ? "\\Domain\\" . $domainName : ""; 
        $namespace .= "\\Entities";

        $template = $this->getTemplate('entity');

        $content = str_replace(
            [
                '{{ namespace }}',
                '{{ entityName }}',
                '{{ constructorParams }}',
                '{{ accessors }}'
            ],
            [
                $namespace,
                $entityName,
                $constructorParams,
                $accessors
            ],
            $template
        );

        $dir = 'src';
        $dir .= ($domainName !== null) ? '/Domain/' . $domainName : "";
        $dir .= '/Entities';
        $this->ensureDirectory($dir);

        // create entity service
        $entityFile = "{$dir}/{$entityName}.php";
        file_put_contents($entityFile, $content);
        $output->writeln("<info>Entity {\$entityName} generated in {$entityFile}</info>");

        // Generate identity value object
        // if ($input->getOption('with-id')) {
        $idInput = new ArrayInput([
            'command' => 'make:domain:value-object-id',
            '--entity-name' => $entityName,
            '--pkg-namespace' => $packageNamespace,
            '--domain-name' => $domainName,
        ]);
        $application->find('make:domain:value-object-id')->run($idInput, $output);
        // }       

        // Optionally generate repository
        if ($input->getOption('with-repo')) {
            $repoInput = new ArrayInput([
                'command' => 'make:domain:repository',
                '--entity-name' => $entityName,
                '--pkg-namespace' => $packageNamespace,
                '--domain-name' => $domainName,
            ]);
            $application->find('make:domain:repository')->run($repoInput, $output);
        }

        // Optionally generate entity create and update services
        if ($input->getOption('with-svc')) {
            $svcInput = new ArrayInput([
                'command' => 'make:domain:entity-service',
                '--entity-name' => $entityName,
                '--pkg-namespace' => $packageNamespace,
                '--domain-name' => $domainName,
            ]);
            $application->find('make:domain:entity-service')->run($svcInput, $output);
        }

        return self::SUCCESS;
    }
}
