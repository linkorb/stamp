<?php

namespace Stamp\Analyzer;

use Stamp\Model\Project;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Alom\Graphviz\Digraph;

class RadvanceSchemaAnalyzer extends Analyzer
{
    public function analyze(Project $project): ?array
    {
        $references = $this->getReferences($project);
        $relativeOutPath = 'doc/schema.svg';

        $path = $this->getFilepath($project, 'app/schema.xml');
        $outPath  = $this->getFilepath($project, $relativeOutPath);

        if (!file_exists($path)) {
            return null;
        }

        $xml = simplexml_load_file($path);
        $graph = new DiGraph('A');

        $tableNames = array();
        $edges = array();

        foreach ($xml->table as $table) {
            $tableNames[] = (string) $table->attributes()['name'];
        }

        foreach ($xml->table as $table) {
            $tableName = (string) $table->attributes()['name'];
            $html = "";

            foreach($table->column as $column) {
                $attributes = $column->attributes();
                $name = (string) $attributes["name"];
                $type = (string) $attributes["type"];
                $doc  = (string) $attributes["doc"];

                $html .= "<tr><td>$name</td><td>$type</td><td>$doc</td></tr>";

                if (isset($references[$name]) && $tableName !== $references[$name]) {
                    $graph->edge(
                        array($tableName, $references[$name])
                    );
                } else if (substr($name, -3) === '_id') {
                    $referencesTo = substr($name, 0, -3);

                    if (in_array($referencesTo, $tableNames)) {
                        $graph->edge(
                            array($tableName, $referencesTo)
                        );
                    } else if (in_array(substr($referencesTo, 0, -1), $tableNames)) {
                        $graph->edge(
                            array($tableName, substr($referencesTo, 0, -1))
                        );
                    }
                }
            }

            $graph
                ->node(
                    $tableName,
                    array(
                        'shape' => 'none',
                        '_escaped' => false,
                        'label' => "<<table><tr><td>$tableName</td></tr>$html</table>>"
                        )
                    );
        }

        $temp = tmpfile();
        $inPath = stream_get_meta_data($temp)['uri'];
        fwrite($temp, $graph->render());

        shell_exec("dot -Tsvg -o " . escapeshellarg($outPath) . ' ' . escapeshellarg($inPath));

        fclose($temp);

        return [
            'schema-dot' => $graph->render(),
            'schema-svg' => $relativeOutPath
        ];
    }

    private function getReferences(Project $project): array
    {
        if (
            isset($project->getVariables()['radvance']['schema']['references'])
        ) {
            return $project->getVariables()['radvance']['schema']['references'];
        } else {
            return array();
        }
    }
}
