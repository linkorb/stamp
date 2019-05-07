<?php

namespace Stamp\Loader;

use Stamp\Model\Project;
use Stamp\Model\Template;

class ArrayProjectLoader
{   
    public function load(array $config, $basePath)
    {
        $project = new Project($basePath, $config['variables'] ?? []);
        
        foreach ($config['templates'] ?? [] as $templateConfig) {
            $template = Template::buildFromConfig($templateConfig);
            $project->addTemplate($template);
        }

        return $project;
    }
}
