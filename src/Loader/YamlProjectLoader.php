<?php

namespace Stamp\Loader;

use Symfony\Component\Yaml\Yaml;

class YamlProjectLoader extends ArrayProjectLoader
{
    public function loadFile($filename)
    {
        $yaml = file_get_contents($filename);
        $basePath = dirname($filename);
        
        $data = Yaml::parse($yaml);
        
        return $this->load($data, $basePath);
    }
}