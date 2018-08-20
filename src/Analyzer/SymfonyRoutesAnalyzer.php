<?php

namespace Stamp\Analyzer;

use Stamp\Model\Project;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

class SymfonyRoutesAnalyzer extends Analyzer
{
    public function analyze(Project $project): ?array
    {
        // Things can fail in the command-line
        // Therefore handling this with care.
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

    private function convertRoutes(array $routeCollection): array
    {
        return array_map(function(array $route) {
            return [
                'path'       => $route['path'],
                'method'     => $route['method'],
                'host'       => $route['host'],
                'controller' => $route['defaults']['_controller']
            ];
        }, $routeCollection);
    }

    private function getAppRoutes(Project $project): ?array
    {
        $out = $this->console($project, "debug:router --format=json");

        if ($out) {
            return json_decode($out, true);
        } else {
            return null;
        }
    }
}
