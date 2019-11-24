<?php

namespace Stamp\Model;

use Stamp\Generator;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Yaml\Yaml;

use LightnCandy\LightnCandy;

use Twig_Environment;
use Twig_Loader_String;
use Twig_Loader_Filesystem;

use Loader\Loader;

class Project
{
    protected $templates = [];
    protected $variables = [];
    protected $expressionLanguage;
    protected $twig;
    protected $basePath;

    public function __construct($basePath, $variables = [], Loader $loader)
    {
        $this->basePath = $basePath;
        $this->variables = $variables;
        $this->loader = $loader;

        $twigLoader = new Twig_Loader_Filesystem($this->getBasePath());

        $this->twig = new Twig_Environment($twigLoader);
    }


    
    public static function buildFromConfig(array $config, $basePath, Loader $loader): self
    {
        $variables = $config['variables'];
        $loader->postProcess($variables, $basePath, []);
        $project = new self($basePath, $variables, $loader);

        foreach ($config['templates'] ?? [] as $templateConfig) {
            $template = Template::buildFromConfig($templateConfig);
            $project->addTemplate($template);
        }

        return $project;
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

    public function generate()
    {
        foreach ($this->getTemplates() as $template) {
            $this->generateTemplate($template);
        }
    }

    public function generateTemplate(Template $template)
    {
        $interpolator = $this->loader->getInterpolator();

        $variables = $this->getVariables();
        $variables = array_merge($variables, $template->getVariables());

        $when = $template->getWhen();
        if ($when) {
            $condition = $interpolator->interpolate('{{' . $when . '}}', $variables);
            if (!$condition) {
                return;
            }
        }

        $items = $template->getItems();
        if ($items) {
            if (is_string($items)) {
                $items = $interpolator->interpolate($items, $variables);
            }
        }

        foreach ($items as $item) {
            $variables['item'] = $item;
            $src = $template->getSrc();
            $dest = $template->getDest();
            $src = $interpolator->interpolate($src, $variables);
            $dest = $interpolator->interpolate($dest, $variables);
            $content = $this->getContent($src);

            $extension = pathinfo($src, PATHINFO_EXTENSION);
            $out = $this->renderContent($content, $extension, $variables);

            $destFilename = $this->normalizeFilename($dest);
            $destPath = dirname($destFilename);

            if (!file_exists($destPath)) {
                mkdir($destPath, 0777, true);
            }
            file_put_contents(
                $destFilename,
                $out
            );
        }
    }

    public function renderContent(string $content, string $extension, array $variables): string
    {
        switch ($extension) {
            case 'twig':
                $template = $this->twig->createTemplate($content);
                return $template->render($variables);
            case 'hbs':
            case 'handlebars':
                $renderWith = LightnCandy::FLAG_HANDLEBARSJS;
                break;
            case 'mustache':
                $renderWith = LightnCandy::FLAG_MUSTACHE;
                break;
            default:
                return $content;
        }

        if (isset($renderWith)) {
            $t = LightnCandy::compile(
                $content,
                [
                    'flags' => $renderWith,
                    'helpers' => [
                        'eq' => function () {
                            // Get arguments
                            $args = func_get_args();
                            $context = $args[count($args) - 1];
                            if ((string) $args[0] === (string) $args[1] ) {
                                // Arguments match, render it
                                return $context['fn']();
                            } else {
                                // If an {{else}} exists, render that instead; otherwise, render nothing
                                return $context['inverse'] ? $context['inverse']() : '';
                            }
                        }
                    ]
                ]
            );
            $renderer = LightnCandy::prepare($t);

            // force array instead of object
            $variables2 = [];
            foreach ($variables as $k=>$v) {
                $variables2[$k] = json_decode(json_encode($v), true);
            }

            return $renderer($variables2, []);
        }
        return null;
    }


    public function normalizeFilename(string $filename): string
    {
        if (substr($filename, 0, 4)=='http') {
            return $filename;
        }
        if (substr($filename, 0, 1)=='/') {
            // absolute path
            return $filename;
        }

        return $this->getBasePath() . '/' . $filename;
    }

    public function getContent(string $filename): string
    {
        $filename = $this->normalizeFilename($filename);
        return file_get_contents($filename);
    }
}
