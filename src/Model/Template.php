<?php

namespace Stamp\Model;

class Template
{
    protected $src;
    protected $dest;
    protected $items;
    protected $variables;

    private function __construct()
    {
    }

    public static function buildFromConfig(array $config): Template
    {
        $file = new self();
        $file->src = $config['src'] ?? null;
        $file->dest = $config['dest'] ?? null;
        $file->items = $config['items'] ?? null;
        $file->variables = $config['variables'] ?? [];
        return $file;
    }

    public function getSrc(): string
    {
        return $this->src;
    }

    public function getDest(): string
    {
        return $this->dest;
    }

    public function getVariables(): array
    {
        return $this->variables;
    }

    public function getItems()
    {
        return $this->items;
    }
}
