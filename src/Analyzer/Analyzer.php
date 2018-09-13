<?php

namespace Stamp\Analyzer;

use Stamp\Model\Project;

abstract class Analyzer
{
    public abstract function analyze(Project $project): ?array;

    protected function maybeGetContent(Project $project, string $filename, callable $parser = null): ?array {
        $path = $project->getFilepath($filename);

        if (is_dir($path)) {
            return [$filename => true];
        } else if (file_exists($path)) {
            $content = file_get_contents($path);
            
            if ($parser) {
                $content = $parser($content);
            }

            return [$filename => $content];
        } else {
            return null;
        }
    }

    protected function hasConsole(Project $project): bool {
        return file_exists(
            $project->getFilepath('bin/console')
        );
    }
    protected function console(Project $project, string $cmd): ?string {
        $console = $project->getFilepath('bin/console');

        if (!$project->hasConsole()) {
            return null;
        }

        return shell_exec("$console $cmd");
    }
}
