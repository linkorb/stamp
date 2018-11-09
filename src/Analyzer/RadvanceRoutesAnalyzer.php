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
            if ($appRoutes = $this->getAppRoutes($project)) {
                return ['route_collection' => $this->convertRoutes($appRoutes)];
            } else {
                return null;
            }
        } catch (Exception $e) {
            return null;
        }
    }

    private function convertRoutes(RouteCollection $routeCollection): array
    {
        return array_map(function(Route $route) {
            return [
                'path'       => $route->getPath(),
                'method'     => $route->getMethods() ? join('|', $route->getMethods()) : 'ANY',
                'controller' => current($route->getDefaults()), // _controller seems unreachable..
                'host'       => $route->getHost() ? $route->getHost() : 'ANY'
            ];
        }, $routeCollection->all());
    }

    protected function getAppRoutes(Project $project): ?RouteCollection
    {
        $autoloadPath = $project->getFilepath('vendor/autoload.php');

        if (!file_exists($autoloadPath)) {
            return null;
        }

        require_once($autoloadPath);

        $envPath = $project->getFilepath('.env');
        if (file_exists($envPath)) {
            $dotenv = new DotEnv();
            $dotenv->load($envPath);
        }

        $appPath = $project->getFilepath('app/bootstrap.php');

        if (file_exists($appPath)) {
            return (require($appPath))['routes'];
        } else {
            return null;
        }
    }
}
