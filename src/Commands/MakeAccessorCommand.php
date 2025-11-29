<?php

namespace DevTools\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeAccessorCommand extends Command
{
    protected static $defaultName = 'make:accessors';

    protected function configure()
    {
        $this
            ->setDescription('Generate accessor methods without get-prefix for private properties and inject them into the class')
            ->addArgument('file', InputArgument::REQUIRED, 'Path to the PHP class file');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');

        if (!file_exists($file)) {
            $output->writeln("<error>File not found: $file</error>");
            return Command::FAILURE;
        }

        $contents = file_get_contents($file);
        $properties = [];

        // --- 1. Match normal private properties ---
        preg_match_all('/private\s+([\w\\\\|?]+)\s+\$(\w+);/', $contents, $matches1);
        if (!empty($matches1[2])) {
            foreach ($matches1[2] as $i => $name) {
                $properties[$name] = $matches1[1][$i];
            }
        }

        // --- 2. Match constructor-promoted properties ---
        preg_match_all('/__construct\s*\((.*?)\)/s', $contents, $matches2);
        if (!empty($matches2[1])) {
            $params = $matches2[1][0];
            $paramsArray = preg_split('/,(?![^\(]*\))/', $params);
            foreach ($paramsArray as $param) {
                if (preg_match('/private\s+([\w\\\\|?]+)\s+\$(\w+)/', trim($param), $m)) {
                    $properties[$m[2]] = $m[1];
                }
            }
        }

        if (empty($properties)) {
            $output->writeln("<comment>No private properties found.</comment>");
            return Command::SUCCESS;
        }

        // --- 3. Detect existing methods to avoid duplicates ---
        preg_match_all('/public function (\w+)\s*\(/', $contents, $existingMethods);
        $existingMethods = $existingMethods[1] ?? [];

        $methodsToAdd = [];
        foreach ($properties as $name => $type) {
            if (!in_array($name, $existingMethods)) {
                $methodsToAdd[] = <<<PHP

    public function {$name}(): {$type}
    {
        return \$this->{$name};
    }

PHP;
            }
        }

        if (empty($methodsToAdd)) {
            $output->writeln("<comment>All accessors already exist.</comment>");
            return Command::SUCCESS;
        }

        // --- 4. Insert methods before last closing brace of the class ---
        $lastBracePos = strrpos($contents, '}');
        if ($lastBracePos === false) {
            $output->writeln("<error>Could not find closing brace of the class.</error>");
            return Command::FAILURE;
        }

        $newContents = substr($contents, 0, $lastBracePos)
            . implode("\n", $methodsToAdd)
            . "\n}";

        // --- 5. Save updated file ---
        file_put_contents($file, $newContents);

        $output->writeln("<info>Injected accessor methods into $file:</info>");
        foreach ($methodsToAdd as $method) {
            $output->writeln($method);
        }

        return Command::SUCCESS;
    }
}
