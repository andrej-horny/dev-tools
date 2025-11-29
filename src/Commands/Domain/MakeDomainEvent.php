<?php

namespace DevTools\Commands\Domain;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeDomainEventCommand extends BaseGeneratorCommand
{
    protected static $defaultName = 'make:domain-event';

    protected function configure()
    {
        $this
            ->setDescription('Generate a DDD DomainEvent class')
            ->addArgument('name', InputArgument::REQUIRED)
            ->addArgument('namespace', InputArgument::OPTIONAL)
            ->addArgument('properties', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $className = $input->getArgument('name');
        $namespace = $input->getArgument('namespace') ?: $this->config['default_namespaces']['domain_event'];
        $propsArg = $input->getArgument('properties') ?? '';
        $props = [];

        foreach (array_filter(array_map('trim', explode(',', $propsArg))) as $prop) {
            if (preg_match('/^([\w\\\\|?]+)\s+\$(\w+)$/', $prop, $matches)) {
                $props[] = ['type' => $matches[1], 'name' => $matches[2]];
            }
        }

        // DomainEvents always have occurredAt timestamp
        $extraParams = [['type' => '\DateTimeImmutable', 'name' => 'occurredAt']];

        $file = $this->generateClass('domain_event', $className, $namespace, $props, $extraParams);
        $output->writeln("<info>DomainEvent {$className} generated in {$file}</info>");

        return self::SUCCESS;
    }
}
