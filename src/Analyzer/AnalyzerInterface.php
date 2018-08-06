<?php

namespace Stamp\Analyzer;

use Stamp\Model\Project;

interface AnalyzerInterface
{
    public function analyze(Project $project);
}