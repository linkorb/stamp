<?php

namespace Stamp\Analyzer;

use Stamp\Model\Project;

class DockerComposeYmlAnalyzer extends Analyzer
{
    public function analyze(Project $project): ?array
    {
        return $this->maybeGetContent($project, 'docker-compose.yml');
    }
}
