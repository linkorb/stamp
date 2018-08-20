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

    protected function hasConsole(Project $project): bool {
        return file_exists(
            $this->getFilepath($project, 'bin/console')
        );
    }
    protected function console(Project $project, string $cmd): ?string {
        $console = $this->getFilepath($project, 'bin/console');

        if (!$this->hasConsole($project)) {
            return null;
        }

        return shell_exec("$console $cmd");
    }
}
