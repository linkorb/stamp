<?php

namespace Stamp\Analyzer;

use Stamp\Model\Project;

class ComposerJsonAnalyzer extends Analyzer
{
    public function analyze(Project $project): ?array
    {
        return $this->maybeGetContent($project, 'composer.json', function($data) {
            return json_decode($data, true);
        });
    }
}
