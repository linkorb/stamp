<?php

namespace Stamp\Model;

use Stamp\Analyzer;

class Project
{
    protected $files = [];
    protected $variables = [];
    protected $basePath;

    protected $analyzerClasses = [
        Analyzer\AnonymizerYmlAnalyzer::class,
        Analyzer\BowerJsonAnalyzer::class,
        Analyzer\CircleciConfigYmlAnalyzer::class,
        Analyzer\ComposerJsonAnalyzer::class,
        Analyzer\DockerComposeYmlAnalyzer::class,
        Analyzer\DockerfileAnalyzer::class,
        Analyzer\DotEnvAnalyzer::class,
        Analyzer\EditorconfigAnalyzer::class,
        Analyzer\FixturesAnalyzer::class,
        Analyzer\MakefileAnalyzer::class,
        Analyzer\PackageJsonAnalyzer::class,
        Analyzer\RadvanceRoutesAnalyzer::class
    ];

    public function __construct($basePath, $variables = [])
    {
        $this->basePath = $basePath;
        $this->variables = $variables;
    }

    public function getVariables()
    {
        return $this->variables;
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
}
