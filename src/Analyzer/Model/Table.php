<?php

namespace Stamp\Analyzer\Model;

class Table
{
    public $identifier;
    public $name;
    public $columns;

    function __construct(string $identifier, string $name, array $columns = []) {
        $this->identifier = $identifier;
        $this->name = $name; 
        $this->columns = $columns;
    }

    public function toHTML(): string {
        $columnsHTML = array_reduce(
            $this->columns, function(string $html, Column $column) {
                return $html . $column->toHTML();
            },
            ''
        );

        return "<table><tr><td>$this->name</td></tr>$columnsHTML</table>";
    }
}
