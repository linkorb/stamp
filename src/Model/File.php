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

    public function getTemplate()
    {
        return $this->template;
    }

    public function getVariables()
    {
        return $this->variables;
    }
}