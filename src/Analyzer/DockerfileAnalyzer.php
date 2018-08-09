<?php

namespace Stamp\Analyzer;

use Stamp\Model\Project;

class DockerfileAnalyzer extends Analyzer
{
    public function analyze(Project $project): ?array
    {
        return $this->maybeGetContent($project, 'Dockerfile');
    }
}
