<?php

namespace Stamp\Analyzer;

use Stamp\Model\Project;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

class RadvanceRoutesAnalyzer extends Analyzer
{
    public function analyze(Project $project): ?array
    {
        // Things can fail in the bootstrap-file itself.
        // Therefore handling Radvance with care.
        try {
            if ($routes = $this->getAppRoutes($project)) {
                return ['route_collection' => $this->convertRoutes($routes)];
            } else {
                return null;
            }
        } catch(Exception $e) {
            return null;
        }
    }

    private function convertRoutes(RouteCollection $routeCollection): array
    {
        return array_map(function(Route $route) {
            return [
                'path'     => $route->getPath(),
                'methods'  => $route->getMethods(),
                'defaults' => $route->getDefaults()
            ];
        }, $routeCollection->all());
    }

    private function getAppRoutes(Project $project): ?RouteCollection
    {
        $autoloadPath = $this->getFilepath($project, 'vendor/autoload.php');

        if (!file_exists($autoloadPath)) {
            return null;
        }

        require_once($autoloadPath);
    
        $envPath = $this->getFilepath($project, '.env');
        if (file_exists($envPath)) {
            $dotenv = new DotEnv();
            $dotenv->load($envPath);
        }

        $appPath = $this->getFilepath($project, 'app/bootstrap.php');

        if (file_exists($appPath)) {
            return require($appPath)['routes'];
        } else {
            return null;
        }
    }
}
