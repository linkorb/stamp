<?php

namespace Stamp\Model;

class File
{
    protected $name;
    protected $template;
    protected $variables;

    public function __construct($name, $template, $variables)
    {
        $this->name = $name;
        $this->template = $template;
        $this->variables = $variables;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getExtensions(): array
    {
        $extensions = explode('.', $this->getName());
        return array_splice($extensions, 1);
    }

    public function hasExtension(string $ext): bool
    {
        return array_search($ext, $this->getExtensions()) !== false;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function getVariables()
    {
        return $this->variables;
    }
}
