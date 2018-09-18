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

    public function getTemplateExtensions(): array
    {
        $extensions = explode('.', $this->getTemplate());
        return array_splice($extensions, 1);
    }

    public function hasTemplateExtension(string $ext): bool
    {
        return array_search($ext, $this->getTemplateExtensions()) !== false;
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
