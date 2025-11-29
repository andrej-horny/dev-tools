<?php

namespace DevTools\Commands\Domain;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MakeEntityCommand extends BaseGeneratorCommand
{
    protected static $defaultName = 'make:entity';

    protected function configure()
    {
        $this
            ->setDescription('Generate a DDD Entity class')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'Entity name')
            ->addOption('namespace', null, InputOption::VALUE_OPTIONAL, 'Namespace')
            ->addOption('props', null, InputOption::VALUE_OPTIONAL, 'Comma separated props')
            // ->addOption('with-id', null, InputOption::VALUE_NONE, 'Also generate the entity ID value object')
            ->addOption('with-repo', null, InputOption::VALUE_NONE, 'Also generate the repository for this entity')
            ->addOption('with-svc', null, InputOption::VALUE_NONE, 'Also generate the services for this entity');
        // ->addOption('with-vo', null, InputOption::VALUE_NONE, 'Also generate property value objects');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $application = $this->getApplication();
        $className = $input->getOption('name');
        $namespace = $input->getOption('namespace') ?: $this->config['default_namespaces']['entity'];
        $propsArg = $input->getOption('props') ?? '';
        $props = [];

        foreach (array_filter(array_map('trim', explode(',', $propsArg))) as $prop) {
            if (preg_match('/^([\w\\\\|?]+)\s+\$(\w+)$/', $prop, $matches)) {
                $props[] = ['type' => $matches[1], 'name' => $matches[2]];
            }
        }

        $idFile = $this->generateIdClass($className, $namespace);
        $output->writeln("<info>ValueObject {$className}Id generated in {$idFile}</info>");

        $file = $this->generateClass('entity', $className, $namespace, $props);
        $output->writeln("<info>Entity {$className} generated in {$file}</info>");

        // Optionally generate repository
        if ($input->getOption('with-repo')) {
            $repoInput = new ArrayInput([
                'command' => 'make:repository',
                '--name' => $className,
            ]);
            $application->find('make:repository')->run($repoInput, $output);
        }

        // Optionally generate repository
        if ($input->getOption('with-svc')) {
            $svcInput = new ArrayInput([
                'command' => 'make:entity-service',
                '--name' => $className,
            ]);
            $application->find('make:entity-service')->run($svcInput, $output);
        }

        return self::SUCCESS;
    }

    protected function generateIdClass($className, $namespace)
    {
        $idClassName = $className . 'Id';
        $props = [
            ['type' => 'string', 'name' => 'value']
        ];
        $file = $this->generateClass('value_object_id', $idClassName, $namespace, $props);

        return $file;
    }
}
