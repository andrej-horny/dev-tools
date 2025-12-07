<?php

namespace DevTools\Commands\Infrastructure;

use Symfony\Component\Console\Command\Command;

abstract class BaseGeneratorCommand extends Command
{
    protected array $config;

    public function __construct()
    {
        parent::__construct();
        $this->config = require __DIR__ . '/../../../config/infrastructure.php';
    }

    protected function getTemplate(string $type): string|null
    {
        $templatePath = $this->config['templates'][$type] ?? null;
        if (!$templatePath || !file_exists($templatePath)) {
            throw new \RuntimeException("Template not found for {$type} at {$templatePath}");
        }

        return file_get_contents($templatePath);
    }

    protected function getContent(array $placeholders, array $values, string $template): string|null
    {
        return str_replace(
            $placeholders,
            $values,
            $template
        );
    }

    protected function parseConstructorParams(array $props): string|null
    {
        $constructorParams = '';

        foreach ($props as $prop) {
            $visibility = isset($prop['visibility']) ? $prop['visibility'] : "private";
            $constructorParams .= "       {$visibility} {$prop['type']} \${$prop['name']},\n";
        }

        return $constructorParams;
    }

    protected function parseAccessors(array $props): string|null
    {
        $accessors = '';

        foreach ($props as $prop) {
            $accessors .= <<<PHP

    public function {$prop['name']}(): {$prop['type']}
    {
        return \$this->{$prop['name']};
    }

PHP;
        }

        return $accessors;
    }

    private function createFile(string $fileName, $content): void
    {
        file_put_contents($fileName, $content);
    }

    protected function ensureDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }
}
