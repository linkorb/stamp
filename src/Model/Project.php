<?php

namespace Stamp\Model;

class Project
{
    protected $files = [];
    protected $variables = [];
    protected $basePath;

    public function __construct($basePath, $variables = [])
    {
        $this->basePath = $basePath;
        $this->variables = $variables;
    }

    public function getVariables()
    {
        return $this->variables;
    }

    public function getBasePath()
    {
        return $this->basePath;
    }

    public function addFile(File $file)
    {
        $this->files[$file->getName()] = $file;
    }
    public function getFiles()
    {
        return $this->files;
    }
}