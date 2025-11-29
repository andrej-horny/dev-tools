<?php

namespace DevTools\Commands\Domain;

use Symfony\Component\Console\Command\Command;

abstract class BaseGeneratorCommand extends Command
{
    protected array $config;

    public function __construct()
    {
        parent::__construct();
        $this->config = require __DIR__ . '/../../../config/domain.php';
    }

    /**
     * Generate class file from template
     *
     * @param string $type Entity|ValueObject|Aggregate|DomainEvent
     * @param string $className
     * @param string $namespace
     * @param array $props Array of ['type' => 'string', 'name' => 'firstName']
     * @param array $extraParams Extra constructor parameters like 'id' or 'occurredAt'
     */
    protected function generateClass(string $type, string $className, string $namespace, array $props, array $extraParams = []): string
    {
        $templatePath = $this->config['templates'][$type] ?? null;
        if (!$templatePath || !file_exists($templatePath)) {
            throw new \RuntimeException("Template not found for {$type} at {$templatePath}");
        }

        $template = file_get_contents($templatePath);

        $properties = ''; // Optional, for non-promoted properties
        $constructorParams = '';
        $accessors = '';

        foreach ($extraParams as $param) {
            $constructorParams .= "        private {$param['type']} \${$param['name']},\n";
            $accessors .= <<<PHP

    public function {$param['name']}(): {$param['type']}
    {
        return \$this->{$param['name']};
    }

PHP;
        }

        foreach ($props as $prop) {
            $constructorParams .= "        private {$prop['type']} \${$prop['name']},\n";
            $accessors .= <<<PHP

    public function {$prop['name']}(): {$prop['type']}
    {
        return \$this->{$prop['name']};
    }

PHP;
        }

        $constructorParams = rtrim($constructorParams, ",\n");

        $content = str_replace(
            [
                '{{namespace}}', 
                '{{className}}', 
                '{{properties}}', 
                '{{constructorParams}}', 
                '{{accessors}}'
            ],
            [
                $namespace, 
                $className, 
                $properties, 
                $constructorParams, 
                $accessors
            ],
            $template
        );

        $dir = $this->config['default_paths'][$type] ?? 'src';
        $this->ensureDirectory($dir);

        $file = "{$dir}/{$className}.php";
        file_put_contents($file, $content);

        return $file;
    }

    private function ensureDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }
}
