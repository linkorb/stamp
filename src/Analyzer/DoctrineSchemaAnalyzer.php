<?php

namespace Stamp\Analyzer;

use Stamp\Model\Project;
use Symfony\Component\Yaml\Yaml;
use Alom\Graphviz\Digraph;

class DoctrineSchemaAnalyzer extends Analyzer
{
    public function analyze(Project $project): ?array
    {
        if (!$this->hasConsole($project)) {
            return null;
        }

        $relativeOutPath = 'doc/schema.svg';
        $outPath  = $this->getFilepath($project, $relativeOutPath);

        $graph = new DiGraph('A');

        $tmpDir = $this->createTmpDir();

        $this->console($project, 'doctrine:mapping:convert yaml ' . escapeshellarg($tmpDir));

        $entities = array_reduce(
            glob("$tmpDir/*.yml"),
            function(array $array, string $path): array {
                return $array = array_merge(
                    $array, Yaml::parse(
                        file_get_contents($path)
                    )
                );
            },
            array()
        );

        \Psy\Shell::debug(get_defined_vars());

        $this->cleanTmpDir($tmpDir);

        $tables = array();

        foreach($entities as $tableId => $table) {
            $tables[$tableId] = $table['table'];
        }

        foreach($entities as $tableId => $table) {
            $html = '';
            $tableName = $table['table'];

            $columns = array_merge($table['id'], $table['fields']);

            foreach($columns as $name => $column) {
                $html .= $this->columnHtml($name, $column);
            }

            if (isset($table['manyToOne'])) {
                foreach($table['manyToOne'] as $referencesTo => $reference) {
                    foreach ($reference['joinColumns'] as $referencesFromColumn => $referencesToColumn) {
                        $html .= $this->relationColumnHtml(
                            $referencesFromColumn,
                            "$tableName:$referencesFromColumn -&gt; {$tables[$reference['targetEntity']]}:{$referencesToColumn['referencedColumnName']}"
                        );
                    }
                    $graph->edge([$tableId, $reference['targetEntity']]);
                }
            }

            if (isset($table['manyToMany'])) {
                foreach($table['manyToMany'] as $reference) {
                    $joinTableName = $reference['joinTable']['name'];
                    if (!isset($tables[$joinTableName])) {
                        $tables[$joinTableName] = $joinTableName;

                        $joinColumnsHtml = join('', array_map(
                            function($column) {
                                return $this->relationColumnHtml($column['name'], "{$column['name']} -&gt; {$column['referencedColumnName']}");
                            },
                            array_merge(
                                $reference['joinTable']['joinColumns'],
                                $reference['joinTable']['inverseJoinColumns']
                            )
                        ));

                        $graph->node(
                            $joinTableName,
                            array(
                                'shape' => 'none',
                                '_escaped' => false,
                                'label' => "<<table><tr><td>$joinTableName</td></tr>$joinColumnsHtml</table>>"
                            )
                        );
                    }

                    $graph->edge([$joinTableName, $reference['targetEntity']]);
                    $graph->edge([$reference['targetEntity'], $joinTableName]);
                }
            }

            $graph->node(
                $tableId,
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

    private function createTmpDir(): string {
        $tmpDir = sys_get_temp_dir();
        $newPath = $tmpDir . '/stamp';
        $this->cleanTmpDir($newPath);
        return $newPath;
    }

    private function cleanTmpDir(string $tmpDir): void {
        if (file_exists($tmpDir)) {
            array_map('unlink', glob("$tmpDir/*"));
        } else {
            mkdir($tmpDir);
        }
    }

    private function columnHtml(string $name, array $column): string {
        $type = isset($column['type']) ? $column['type'] : '';
        $doc = $this->generateDoc($column);

        return "<tr><td>$name</td><td>$type</td><td>$doc</td></tr>";
    }

    private function generateDoc(array $column): string {
        return join(', ', array_map(
            function($key) use ($column) {
                $val = $column[$key];
                if ($val === null) {
                    return "$key: null";
                } else if ($val === true) {
                    return "$key: true";
                } else if ($val === false) {
                    return "$key: false";
                } else {
                    return "$key: $val";
                }
            }, ['length', 'unique', 'nullable']
        ));
    }

    private function relationColumnHtml($name, $doc) {
        return "<tr><td>$name</td><td>reference</td><td>$doc</td></tr>";
    }
}
