<?php

namespace Stamp\Model;

class Project
{
    protected $files = [];
    protected $config = [];
    protected $analyzedData = [];
    protected $basePath;

    public function __construct($basePath, $config = [])
    {
        $this->basePath = $basePath;
        $this->config = $config;
    }

    public function getVariables(): array
    {
        return $this->config['variables'] ?? [];
    }

    public function getConfig(): array {
        return $this->config;
    }

    public function getBasePath()
    {
        return $this->basePath;
    }

    public function addFile(File $file)
    {
        $this->files[$file->getName()] = $file;
    }
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param array $analyzedData
     */
    public function setAnalyzedData(array $analyzedData): void
    {
        $this->analyzedData = $analyzedData;
    }

    /**
     * @return array
     */
    public function getAnalyzedData(): array
    {
        return $this->analyzedData;
    }

    public function getFilepath(string $filename): string {
        return $this->getBasePath() . '/' . $filename;
    }

    public function hasConsole(): bool
    {
        return file_exists(
            $this->getFilepath('bin/console')
        );
    }

    public function console(string $cmd): ?string {
        $console = $this->getFilepath('bin/console');

        if (!$this->hasConsole()) {
            return null;
        }

        return shell_exec("$console $cmd");
    }
}
