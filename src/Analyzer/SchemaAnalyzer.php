<?php

namespace Stamp\Analyzer;

use Stamp\Model\Project;
use Alom\Graphviz\Digraph;

abstract class SchemaAnalyzer extends Analyzer
{
    protected function saveGraph(Digraph $graph, Project $project): array {
        $rendered = $graph->render();

        $relativeOutPath = 'doc/schema.svg';
        $outPath  = $project->getFilepath($relativeOutPath);

        $temp = tmpfile();
        $inPath = stream_get_meta_data($temp)['uri'];
        fwrite($temp, $rendered);

        shell_exec("dot -Tsvg -o " . escapeshellarg($outPath) . ' ' . escapeshellarg($inPath));

        fclose($temp);

        return [ 'schema-dot' => $rendered,
                 'schema-svg' => $relativeOutPath ];
    }
}
