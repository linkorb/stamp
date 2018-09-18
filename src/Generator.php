<?php

namespace Stamp;

use Stamp\Model\Project;
use Stamp\Model\File;

class Generator
{
    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function generate()
    {
        foreach ($this->project->getFiles() as $file) {
            $this->generateFile($file);
        }
    }
    
    protected function process(&$item, &$key)
    {
        if ($item[0]=='@') {
            $filename = substr($item, 1);
            $item = $this->loadString($filename);
        }
    }

    public function generateFile(File $file)
    {
        $templateString = $this->loadString($file->getTemplate());

        $twig = new \Twig_Environment(new \Twig_Loader_String());
        $data = array_replace_recursive($this->project->getVariables(), $file->getVariables());

        array_walk_recursive($data, [$this, 'process']);
        $data = array_merge_recursive(
            $data,
            ['analyzer' => $this->project->analyze()]
        );
        $rendered = $twig->render($templateString, $data);

        file_put_contents($this->project->getBasePath() . '/' . $file->getName(), $rendered);
        //echo $rendered;
    }

    public function loadString($template)
    {
        if (substr($template, 0, 4)=='http') {
            // Load over HTTP
            return file_get_contents($template);
        }
        if (substr($template, 0, 1)=='/') {
            // absolute path
            return file_get_contents($template);
        }
        
        // relative path
        return file_get_contents($this->project->getBasePath() . '/' . $template);
    }
}
