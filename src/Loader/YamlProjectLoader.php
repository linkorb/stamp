<?php

namespace Stamp\Loader;

use Symfony\Component\Yaml\Yaml;
use RuntimeException;

class YamlProjectLoader extends ArrayProjectLoader
{
    public function loadFile($filename)
    {
        if (!is_file($filename)) {
            throw new RuntimeException("filename is not a file: " . $filename);
        }
        $yaml = file_get_contents($filename);
        $basePath = dirname($filename);
        
        $data = Yaml::parse($yaml);
        
        return $this->load($data, $basePath);
    }
}