<?php

namespace Stamp\Model;

class Template
{
    protected $src;
    protected $dest;
    protected $items;
    protected $when;
    protected $variables;

    private function __construct()
    {
    }

    public static function buildFromConfig(array $config): Template
    {
        $file = new self();
        $file->src = $config['src'] ?? null;
        $file->dest = $config['dest'] ?? null;

        $items = ['default'];
        if (isset($config['items'])) {
            $items = $config['items'];
        }
        $file->items = $items;
        $file->when = $config['when'] ?? null;

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

    public function getWhen(): ?string
    {
        return $this->when;
    }
}
