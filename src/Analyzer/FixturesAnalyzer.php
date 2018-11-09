<?php

namespace Stamp\Analyzer;

use Stamp\Model\Project;

class FixturesAnalyzer extends Analyzer
{
    public function analyze(Project $project): ?array
    {
        return $this->maybeGetContent($project, 'fixtures');
    }
}
