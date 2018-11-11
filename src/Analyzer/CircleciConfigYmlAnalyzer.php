<?php

namespace Stamp\Analyzer;

use Symfony\Component\Yaml\Yaml;
use Stamp\Model\Project;

class CircleciConfigYmlAnalyzer extends Analyzer
{
    public function analyze(Project $project): ?array
    {
        return $this->maybeGetContent($project, '.circleci/config.yml', function($data) {
            return Yaml::parse($data);
        });
    }
}
