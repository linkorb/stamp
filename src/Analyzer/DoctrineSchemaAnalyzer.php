<?php

namespace Stamp\Analyzer;

use Stamp\Analyzer\Model\Table;
use Stamp\Analyzer\Model\Column;
use Stamp\Analyzer\Model\Database;

use Stamp\Model\Project;
use Symfony\Component\Yaml\Yaml;

class DoctrineSchemaAnalyzer extends SchemaAnalyzer
{
    public function analyze(Project $project): ?array
    {
        if (!$project->hasConsole()) {
            return null;
        }

        $tmpDir = $this->createTmpDir();

        $project->console('doctrine:mapping:convert yaml ' . escapeshellarg($tmpDir));

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

        $this->cleanTmpDir($tmpDir);

        $graph = $this->buildDatabase($entities)->toGraph();

        return $this->saveGraph($graph, $project);
    }

    public function buildDatabase(array $entities) {
        $database = new Database();
        $conversion = [];

        foreach($entities as $id => $tableProps) {
            $conversion[$id] = $tableProps['table'];
        }

        foreach($entities as $id => $tableProps) {
            $table = new Table(
                $id,
                $tableProps['table']
            );

            foreach($tableProps['fields'] as $name => $columnProps) {
                $name = isset($columnProps['column']) ? $columnProps['column'] : $name;
                $type = isset($columnProps['type'])   ? $columnProps['type'] : '';

                unset($columnProps['type']);
                unset($columnProps['column']);
    
                $table->columns[] = new Column(
                    $name,
                    $type,
                    array_map(
                        function ($prop, $value) {
                            if ($value === true)  return "{$prop}: true";
                            if ($value === false) return "{$prop}: false";
                            if ($value === null)  return "{$prop}: NULL";

                            return "{$prop}: {$value}";
                        },
                        array_keys($columnProps),
                        $columnProps
                    )
                );
            }

            // manyToOne (just a column)
            foreach(
                isset($tableProps['manyToOne']) ? $tableProps['manyToOne'] : [] as $manyToOne
            ) {
                $targetId = $manyToOne['targetEntity'];
                foreach ($manyToOne['joinColumns'] as $columnName => $reference) {
                    $table->columns[] = new Column(
                        $columnName,
                        'reference',
                        ["references to {$conversion[$targetId]}:{$reference["referencedColumnName"]}"]
                    );
                }

                $database->references[] = [$table->identifier, $targetId];
            }

            // oneToMany: see manyToOne

            // manyToMany (join table)
            foreach(
                isset($tableProps['manyToMany']) ? $tableProps['manyToMany'] : [] as $manyToMany
            ) {
                $targetId = $manyToMany['targetEntity'];
                $joinTableProps = $manyToMany['joinTable'];

                if (!$database->tableCreated($joinTableProps['name'])) {
                    $joinTable = new Table($joinTableProps['name'], $joinTableProps['name']);

                    foreach(array_merge($joinTableProps['joinColumns'], $joinTableProps['inverseJoinColumns']) as $joinColumnProps) {
                        $joinTable->columns[] = new Column(
                            $joinColumnProps['name'],
                            'reference',
                            ["references to {$joinColumnProps["referencedColumnName"]}"]
                        );
                    }

                    $database->tables[] = $joinTable;

                    $database->references[] = [$targetId, $joinTable->identifier];
                    $database->references[] = [$joinTable->identifier, $targetId];

                    $database->references[] = [$table->identifier, $joinTable->identifier];
                    $database->references[] = [$joinTable->identifier, $table->identifier];
                }
            }

            $database->tables[] = $table;
        }

        return $database;
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
}
