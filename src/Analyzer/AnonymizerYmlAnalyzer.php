<?php

namespace Stamp\Analyzer;

use Stamp\Model\Project;

class AnonymizerYmlAnalyzer extends Analyzer
{
    public function analyze(Project $project): ?array
    {
        return $this->maybeGetContent($project, 'anonymizer.yml');
    }
}
