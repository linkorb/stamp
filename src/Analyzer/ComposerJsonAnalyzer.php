<?php

namespace Stamp\Analyzer;

use Stamp\Model\Project;

class ComposerJsonAnalyzer implements AnalyzerInterface
{
    public function analyze(Project $project)
    {
        $filename = $project->getBasePath() . '/composer.json';
        if (!file_exists($filename)) {
            return null;
        }
        $content = file_get_contents($filename);
        $data = json_decode($content, true);
        return ['composer.json' => $data];
    }
}