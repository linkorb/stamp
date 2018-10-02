<?php

namespace Stamp\Loader;

use Stamp\Model\Project;
use Stamp\Model\File;

class ArrayProjectLoader
{   
    public function load($data, $basePath)
    {
        $project = new Project($basePath, $data);
        
        foreach ($data['files'] ?? [] as $name => $fileData) {
            $template = $fileData['template'] ?? null;
            $variables = $fileData['variables'] ?? [];
            $file = new File($name, $template, $variables);
            $project->addFile($file);
        }
        return $project;
    }
}
