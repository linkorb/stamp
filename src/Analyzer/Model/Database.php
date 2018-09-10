<?php

namespace Stamp\Analyzer\Model;

use Alom\Graphviz\Digraph;

class Database
{
    public $references;
    public $tables;

    function __construct(array $tables = [], array $references = []) {
        $this->tables = $tables; 
        $this->references = $references;
    }

    public function toGraph(): Digraph {
        $graph = new DiGraph('A');

        foreach ($this->tables as $table) {
            $graph->node(
                $table->identifier,
                array(
                    'shape' => 'none',
                    '_escaped' => false,
                    'label' => '<' . $table->toHTML() . '>'
                )
            );
        }

        foreach ($this->references as $ref) {
            $graph->edge($ref);
        }

        return $graph;
    }

    public function tableCreated($name): bool {
        foreach($this->tables as $table) {
            if ($table->name == $name) {
                return true;
            }
        }

        return false;
    }
}
