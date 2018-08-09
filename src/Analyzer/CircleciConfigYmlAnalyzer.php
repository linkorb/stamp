<?php

namespace Stamp\Analyzer;

use Stamp\Model\Project;

class CircleciConfigYmlAnalyzer extends Analyzer
{
    public function analyze(Project $project)
    {
        return $this->maybeGetContent($project, '.circleci/config.yml');
    }
}
