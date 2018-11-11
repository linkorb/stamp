<?php

namespace Stamp\Analyzer;

use Symfony\Component\Yaml\Yaml;
use Stamp\Model\Project;

class AnonymizerYmlAnalyzer extends Analyzer
{
    public function analyze(Project $project): ?array
    {
        return $this->maybeGetContent($project, 'anonymizer.yml', function($data) {
            return Yaml::parse($data);
        });
    }
}
