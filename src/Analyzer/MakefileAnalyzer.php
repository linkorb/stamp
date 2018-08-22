<?php

namespace Stamp\Analyzer;

use Stamp\Model\Project;

class MakefileAnalyzer extends Analyzer
{
    public function analyze(Project $project): ?array
    {
        return $this->maybeGetContent($project, 'Makefile', function($text) {
            return $this->analyzeMakefile($text);
        });
    }

    private function analyzeMakefile(string $text): array
    {
        $results = array();
        preg_match_all("/^(?!\s)(.+)\:.*$/m", $text, $results);

        $comments = array_map(function(string $line) {
            $comment = explode('##', $line);
            if (isset($comment[1])) {
                return ltrim(rtrim($comment[1]));
            } else {
                return null;
            }
        }, $results[0]);

        $tasks = array_combine($results[1], $comments);

        unset($tasks['.PHONY']);
        
        return $tasks;
    }
}
