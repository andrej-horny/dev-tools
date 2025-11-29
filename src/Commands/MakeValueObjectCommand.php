<?php

namespace DevTools\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeValueObjectCommand extends BaseGeneratorCommand
{
    protected static $defaultName = 'make:value-object';

    protected function configure()
    {
        $this
            ->setDescription('Generate a DDD ValueObject class')
            ->addArgument('name', InputArgument::REQUIRED)
            ->addArgument('namespace', InputArgument::OPTIONAL)
            ->addArgument('properties', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $className = $input->getArgument('name');
        $namespace = $input->getArgument('namespace') ?: $this->config['default_namespaces']['value_object'];
        $propsArg = $input->getArgument('properties') ?? '';
        $props = [];

        foreach (array_filter(array_map('trim', explode(',', $propsArg))) as $prop) {
            if (preg_match('/^([\w\\\\|?]+)\s+\$(\w+)$/', $prop, $matches)) {
                $props[] = ['type' => $matches[1], 'name' => $matches[2]];
            }
        }

        $file = $this->generateClass('value_object', $className, $namespace, $props);
        $output->writeln("<info>ValueObject {$className} generated in {$file}</info>");

        return self::SUCCESS;
    }
}
