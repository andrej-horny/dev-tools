<?php

namespace DevTools\Commands\Domain;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MakeAggregateCommand extends BaseGeneratorCommand
{
    protected static $defaultName = 'make:domain:aggregate';

    protected function configure()
    {
        $this
            ->setDescription('Generate a DDD Aggregate class')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'Entity name')
            ->addOption('namespace', null, InputOption::VALUE_OPTIONAL, 'Namespace')
            ->addOption('props', null, InputOption::VALUE_OPTIONAL, 'Comma separated props');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $className = $input->getOption('name');
        $namespace = $input->getOption('namespace') ?: $this->config['default_namespaces']['aggregate'];
        $propsArg = $input->getOption('props') ?? '';
        $props = [];

        foreach (array_filter(array_map('trim', explode(',', $propsArg))) as $prop) {
            if (preg_match('/^([\w\\\\|?]+)\s+\$(\w+)$/', $prop, $matches)) {
                $props[] = ['type' => $matches[1], 'name' => $matches[2]];
            }
        }

        // Aggregates always have an ID
        $extraParams = [['type' => 'string', 'name' => 'id']];

        $file = $this->generateClass('aggregate', $className, $namespace, $props, $extraParams);
        $output->writeln("<info>Aggregate {$className} generated in {$file}</info>");

        return self::SUCCESS;
    }
}
