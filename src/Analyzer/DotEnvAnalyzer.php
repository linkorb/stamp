<?php

namespace Stamp\Analyzer;

use Stamp\Model\Project;

class DotEnvAnalyzer extends Analyzer
{
    public function analyze(Project $project): ?array
    {
        return $this->maybeGetContent($project, '.env.dist', function($text) {
            return $this->analyzeDotEnv($text);
        });
    }

    public function analyzeDotEnv(string $text): array
    {
        $vars = array();

        $lines = explode("\n", $text);
        $comments = array();

        foreach ($lines as $line) {
            if (!$this->trim($line)) {
                continue;
            } else if ($line[0] === '#') {
                $comments[] = $this->trimComment($line);
            } else {
                // Splitting on '=' might cause issues in the future
                // for example, when using BASE64: key="MTIzNA=="
                $commentSplitted = explode('#', $line);

                if (isset($commentSplitted[1])) {
                    $comments[] = $this->trimComment($commentSplitted[1]);
                }

                $varSplitted = explode('=', $commentSplitted[0]);

                $vars[] = $this->newEnvVar(
                    $varSplitted[0],
                    $varSplitted[1],
                    join("\n", $comments)
                );

                $comments = array();
            }
        }

        return $vars;
    }

    private function newEnvVar(string $name = null, string $default = null, string $comment = null): array {
        return [         
            "name"    => $this->trim($name),
            "default" => $this->trim($default),
            "comment" => $this->trim($comment)
        ];
    }

    private function trim(string $text): string
    {
        return ltrim(rtrim($text));
    }

    private function trimComment(string $comment): string
    {
        return $this->trim(substr($comment, 1));
    }
}
