<?php

namespace Stamp\Analyzer;

use Stamp\Model\Project;

abstract class Analyzer
{
    public abstract function analyze(Project $project): ?array;

    protected function getFilepath(Project $project, string $filename): string {
        return $project->getBasePath() . '/' . $filename;
    }

    protected function maybeGetContent(Project $project, string $filename, callable $parser = null): ?array {
        $path = $this->getFilepath($project, $filename);

        if (file_exists($path)) {
            $content = file_get_contents($path);
            
            if ($parser) {
                $content = $parser($content);
            }

            return [$filename => $content];
        } else if (is_dir($path)) {
            return [$filename => true];
        } else {
            return null;
        }
    }
}
