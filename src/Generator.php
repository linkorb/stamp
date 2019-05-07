<?php

namespace Stamp;

use Stamp\Model\Project;
use Stamp\Model\Template;

use LightnCandy\LightnCandy;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

use Twig_Environment;
use Twig_Loader_String;
use Twig_Loader_Filesystem;

use RuntimeException;

class Generator
{
    private $twig;
    private $expressionLanguage;

    public function __construct(Project $project)
    {
        $this->project = $project;
        $loader = new Twig_Loader_String();
        //$loader->addPath($project->getBasePath(), 'project');
        $loader = new Twig_Loader_Filesystem($project->getBasePath());


        $this->twig = new Twig_Environment($loader);
        $this->expressionLanguage = new ExpressionLanguage();

        $this->expressionLanguage->register(
            'dict',
            function ($items) {},
            function ($arguments, $items) {
                $res = [];
                foreach ($items as $key => $value) {
                    $res[] = [
                        'key' => $key,
                        'value' => $value,
                    ];
                }
                return $res;
            }
        );

        $this->expressionLanguage->register(
            'strtolower',
            function ($items) {},
            function ($arguments, $str) {
                return strtolower($str);
            }
        );

    }

    public function generate()
    {
        foreach ($this->project->getTemplates() as $template) {
            $this->generateTemplate($template);
        }
    }

    public function generateTemplate(Template $template)
    {
        $items = $template->getItems();
        $variables = $this->project->getVariables();
        $variables = array_merge($variables, $template->getVariables());
        if ($items) {
            if (is_string($items)) {
                if ($items[0]=='{') {
                    $expression = trim($items, '{} ');
                    
                    $items = $this->expressionLanguage->evaluate($expression, $variables);
                }
            }
        }
        if (!$items) {
            $items = [
                'default'
            ];
        }
        foreach ($items as $item) {
            $variables['item'] = $item;
            $src = $template->getSrc();
            $dest = $template->getDest();
            $src = $this->interpolate($src, $variables);
            $dest = $this->interpolate($dest, $variables);
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

    public function interpolate(string $str, array $variables) {
        preg_match_all('/\{\{(.*?)\}\}/i', $str, $matches, PREG_PATTERN_ORDER);
        for ($i = 0; $i < count($matches[1]); $i++) {
            $expression = trim($matches[1][$i]);

            // turn sub-keys into objects for dot-notation access in expressions
            $variables2 = [];
            foreach ($variables as $k=>$v) {
                $variables2[$k] = json_decode(json_encode($v));
            }
            // evaluate
            $res = $this->expressionLanguage->evaluate($expression, $variables2);
            $str = str_replace($matches[0][$i], $res, $str);
        }
        return $str;
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

            return $renderer($variables, []);
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

        return $this->project->getBasePath() . '/' . $filename;
    }

    public function getContent(string $filename): string
    {
        $filename = $this->normalizeFilename($filename);
        return file_get_contents($filename);
    }
}
