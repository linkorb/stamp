<?php

namespace Stamp\Analyzer\Model;

class Column
{
    public $name;
    public $type;
    public $docs;

    function __construct(string $name, string $type, array $docs = []) {
        $this->name = $name; 
        $this->type = $type;
        $this->docs = $docs;
    }

    public function toHTML(): string {
        $doc = join(",    ", $this->docs);
        return "<tr><td>$this->name</td><td>$this->type</td><td>$doc</td></tr>";
    }
}
