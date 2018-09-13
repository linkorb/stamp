<?php

require_once __dir__ . '/../vendor/autoload.php';

use Stamp\Loader\YamlProjectLoader;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

class AnalyzerTestCase extends PHPUnit\Framework\TestCase {
    protected $fullProject;
    protected $emptyProject;

    protected function setUp() {
        $loader = new YamlProjectLoader();
        $this->fullProject  = $loader->loadFile(__dir__ . '/../examples/full-project/stamp.yml');
        $this->emptyProject = $loader->loadFile(__dir__ . '/../examples/empty-project/stamp.yml');
    }

    public function testEmptyProject(): void
    {
        $this->assertNull(
            $this->analyzer->analyze($this->emptyProject)
        );
    }

    public static function exampleRouteCollection(): RouteCollection
    {
                
        $collection = new RouteCollection();
        $collection->add('foo', new Route('/foo', ['_controller' => 'FooController']));
        $collection->add('bar', new Route('/bar', ['_controller' => 'BarController']));
        $collection->add('foobar', new Route('/foo/bar', ['_controller' => 'BarController']));

        return $collection;
    }
}
