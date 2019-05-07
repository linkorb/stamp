<?php

namespace Stamp\Model;

class Project
{
    protected $templates = [];
    protected $variables = [];
    protected $basePath;

    public function __construct($basePath, $variables = [])
    {
        $this->basePath = $basePath;
        $this->variables = $variables;
    }

    public function getVariables(): array
    {
        return $this->variables ?? [];
    }

    public function setVariables(array $variables): void
    {
        $this->variables = $variables;
    }

    public function getBasePath()
    {
        return $this->basePath;
    }

    public function addTemplate(Template $template)
    {
        $this->templates[] = $template;
    }

    public function getTemplates()
    {
        return $this->templates;
    }

    public function getFilepath(string $filename): string {
        return $this->getBasePath() . '/' . $filename;
    }
}
