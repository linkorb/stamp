<?php

namespace Stamp\Analyzer;

use Stamp\Model\Project;

class RadvanceRoutesAnalyzer extends Analyzer
{
    public function analyze(Project $project): ?array
    {
        try {
            $app = $this->getApp($project);
            return ['route_collection' => $app['routes']];
        } catch(Exception $e) {
            return null;
        }
    }

    private function getApp(Project $project)
    {
        $autoloadPath = $this->getFilepath($project, 'vendor/autoload.php');

        if (!file_exists($autoloadPath)) {
            return null;
        }

        require_once($autoload);
    
        $envPath = $this->getFilepath($project, '.env');
        if (file_exists($envPath)) {
            $dotenv = new Symfony\Component\Dotenv\Dotenv();
            $dotenv->load($envPath);
        }

        $appPath = $this->getFilepath($project, 'app/bootstrap.php');

        if (file_exists($appPath)) {
            return require($appPath);
        } else {
            return null;
        }
    }
}
