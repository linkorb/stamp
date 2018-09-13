<?php

namespace Stamp\Analyzer;

use Stamp\Model\Project;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Alom\Graphviz\Digraph;

use Stamp\Analyzer\Model\Database;
use Stamp\Analyzer\Model\Table;
use Stamp\Analyzer\Model\Column;

class RadvanceSchemaAnalyzer extends SchemaAnalyzer
{
    public function analyze(Project $project): ?array
    {
        $references = $this->getReferences($project);

        $path = $project->getFilepath('app/schema.xml');

        if (!file_exists($path)) {
            return null;
        }

        $xml = simplexml_load_file($path);

        $graph = $this->buildDatabase($xml, $references)->toGraph();

        return $this->saveGraph($graph, $project);
    }

    public function buildDatabase($xml, $references): Database {
        $tableNames = array();
        $database = new Database();

        foreach ($xml->table as $table) {
            $tableNames[] = (string) $table->attributes()['name'];
        }

        foreach ($xml->table as $table) {
            $tableName = (string) $table->attributes()['name'];
            $tableObject = new Table($tableName, $tableName);
            
            foreach($table->column as $column) {
                $attributes = $column->attributes();
                $name = (string) $attributes["name"];
                $type = (string) $attributes["type"];
                $docs  = [(string) $attributes["doc"]];

                if (isset($references[$name]) && $tableName !== $references[$name]) {
                    $database->references[] = [$tableName, $references[$name]];
                    $docs[] = 'references to ' . $references[$name];
                } else if (substr($name, -3) === '_id') {
                    $referencesTo = substr($name, 0, -3);
                    if (in_array($referencesTo, $tableNames)) {
                        $database->references[] = [$tableName, $referencesTo];
                        $docs[] = 'references to ' . $referencesTo;
                    } else if (in_array(substr($referencesTo, 0, -1), $tableNames)) {
                        $database->references[] = [$tableName, substr($referencesTo, 0, -1)];
                        $docs[] = 'references to ' . substr($referencesTo, 0, -1);
                    }
                }

                $tableObject->columns[] = new Column($name, $type, $docs);
            }

            $database->tables[] = $tableObject;
        }
    
        return $database;
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
