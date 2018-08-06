<?php

namespace Stamp\Analyzer;

use Stamp\Model\Project;

class PackageJsonAnalyzer implements AnalyzerInterface
{
    public function analyze(Project $project)
    {
        $filename = $project->getBasePath() . '/package.json';
        if (!file_exists($filename)) {
            return null;
        }
        $content = file_get_contents($filename);
        $data = json_decode($content, true);
        return ['package.json' => $data];
    }
}