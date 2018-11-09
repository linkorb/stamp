<?php

namespace Stamp\Model;

use Stamp\Analyzer;

class Project
{
    protected $files = [];
    protected $variables = [];
    protected $config = [];
    protected $basePath;

    protected $analyzerClasses = [
        Analyzer\AnonymizerYmlAnalyzer::class,
        Analyzer\BowerJsonAnalyzer::class,
        Analyzer\CircleciConfigYmlAnalyzer::class,
        Analyzer\ComposerJsonAnalyzer::class,
        Analyzer\DockerComposeYmlAnalyzer::class,
        Analyzer\DockerfileAnalyzer::class,
        Analyzer\DoctrineSchemaAnalyzer::class,
        Analyzer\DotEnvAnalyzer::class,
        Analyzer\EditorconfigAnalyzer::class,
        Analyzer\FixturesAnalyzer::class,
        Analyzer\GithubAnalyzer::class,
        Analyzer\MakefileAnalyzer::class,
        Analyzer\PackageJsonAnalyzer::class,
        Analyzer\RadvanceRoutesAnalyzer::class,
        Analyzer\RadvanceSchemaAnalyzer::class,
        Analyzer\SymfonyRoutesAnalyzer::class,
    ];

    public function __construct($basePath, $config = [])
    {
        $this->basePath = $basePath;
        $this->config = $config;
    }

    public function getVariables(): array
    {
        if (isset($this->config['variables'])) {
            return $this->config['variables'];
        } else {
            return [];
        }
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

    public function getData()
    {
        return array_merge_recursive(
            $this->variables,
            ['analyzer' => $this->analyze()]
        );
    }

    public function analyze()
    {
        $data = [];
        foreach ($this->analyzerClasses as $analyzerClass) {
            $analyzer = new $analyzerClass();
            $res = $analyzer->analyze($this);
            if ($res) {
                $data = array_merge_recursive($data, $res);
            }
        }
        return $data;
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

    public function git(string $cmd): ?string {
        $gitDir = $this->getFilePath('.git');

        if (!is_dir($gitDir)) {
            return null;
        }

        $dir = escapeshellarg($gitDir);

        return shell_exec("git --git-dir $dir $cmd");
    }
}
