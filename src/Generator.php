<?php

namespace Stamp;

use Stamp\Model\Project;
use Stamp\Model\File;

use LightnCandy\LightnCandy;

use Twig_Environment;
use Twig_Loader_String;

class Generator
{
    private $twig;

    public function __construct(Project $project)
    {
        $this->project = $project;
        $this->twig = new Twig_Environment(new Twig_Loader_String());
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
        file_put_contents(
            $this->project->getBasePath() . '/' . $file->getName(),
            $this->renderFile($file)
        );
    }

    public function renderFile(File $file): string
    {
        $analyzed = ['analyzer' => $this->project->analyze()];
        $config = $this->interpolate($this->project->getConfig(), $analyzed);
        $fileConfig = $this->interpolate($file->getVariables(), $analyzed);
        $blocks = isset($fileConfig['blocks']) ? array_map(
            function($block) {
                return $this->loadString($block);
            }, $fileConfig['blocks']
        ) : [];
        
        $data = array_merge_recursive(
            $analyzed,
            $config,
            $fileConfig,
            ['blocks' => $blocks]
        );

        $templateStringTemplate = $this->twig->createTemplate($file->getTemplate());
        $templateString = $this->loadString($templateStringTemplate->render($data));

        if ($file->hasTemplateExtension('twig')) {
            $template = $this->twig->createTemplate($templateString);
            return $template->render($data);
        } else if ($file->hasTemplateExtension('handlebars')) {
            $renderWith = LightnCandy::FLAG_HANDLEBARSJS;
        } else if ($file->hasTemplateExtension('mustache')) {
            $renderWith = LightnCandy::FLAG_MUSTACHE;
        } else {
            return $templateString;
        }

        if (isset($renderWith)) {
            $t = LightnCandy::compile($templateString, ['flags' => $renderWith]);
            $renderer = LightnCandy::prepare($t);

            return $renderer($data, []);
        }
    }

    public function interpolate(array $config, array $analyzerResults): array {
        $data = array_merge_recursive($config, $analyzerResults);
        $twig = new \Twig_Environment(new \Twig_Loader_String());

        array_walk_recursive($config, function(&$value, $key) use ($data, $twig) {
            $template = $twig->createTemplate($value);
            $value = $template->render($data);
        });

        return $config;
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
