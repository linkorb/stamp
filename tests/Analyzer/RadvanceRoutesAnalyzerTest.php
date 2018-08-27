<?php
declare(strict_types=1);

use Stamp\Model\Project;
use Stamp\Analyzer\RadvanceRoutesAnalyzer as OriginalAnalyzer;
use Symfony\Component\Routing\RouteCollection;

class RadvanceRoutesAnalayzer extends OriginalAnalyzer
{
    protected function getAppRoutes(Project $project): ?RouteCollection
    {
        return \TestCase::exampleRouteCollection();
    }
}

final class RadvanceRoutesAnalyzerTest extends \TestCase
{
    public function testParsingSucceeds(): void
    {
        $analyzer = new RadvanceRoutesAnalayzer();
        $this->assertEquals(
            [
                'foo' => [
                    'path'       => '/foo',
                    'method'     => 'ANY',
                    'controller' => 'FooController',
                    'host'       => 'ANY'
                ],
                'bar' => [
                    'path'       => '/bar',
                    'method'     => 'ANY',
                    'controller' => 'BarController',
                    'host'       => 'ANY'
                ],
                'foobar' => [
                    'path'       => '/foo/bar',
                    'method'     => 'ANY',
                    'controller' => 'BarController',
                    'host'       => 'ANY'
                ],
            ],
            $analyzer->analyze($this->fullProject)['route_collection']
        );
    }
    public function testEmptyProject(): void
    {
        $analyzer = new OriginalAnalyzer();
        $this->assertNull(
            $analyzer->analyze($this->emptyProject)
        );
    }
}
