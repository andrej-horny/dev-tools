<?php

namespace DevTools\Commands;

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
            ->addOption('props', null, InputOption::VALUE_OPTIONAL, 'Comma separated props');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $className = $input->getOption('name');
        $namespace = $input->getOption('namespace') ?: $this->config['default_namespaces']['entity'] . "\\$className";
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

        return self::SUCCESS;
    }

    protected function generateIdClass($className, $namespace)
    {
        $idClassName = $className . 'Id';
        $props = [
            ['type' => 'string', 'name' => 'value']
        ];
        $file = $this->generateClass('value_object', $idClassName, $namespace, $props);

        return $file;
    }
}
