<?php

namespace Stamp\Analyzer;

use Stamp\Model\Project;

class PackageJsonAnalyzer extends Analyzer
{
    public function analyze(Project $project): ?array
    {
        return $this->maybeGetContent($project, 'package.json', function($data) {
            return json_decode($data, true);
        });
    }
}
