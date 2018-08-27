<?php
declare(strict_types=1);

use Stamp\Model\Project;
use Stamp\Analyzer\SymfonyRoutesAnalyzer;
use Symfony\Component\Routing\RouteCollection;

final class SymfonyRoutesAnalyzerTest extends \TestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->analyzer = new SymfonyRoutesAnalyzer();
    }

    public function testParsingSucceeds(): void
    {
        $this->assertEquals(
            [
                'admin_index' => [
                    'path'       => '/{_locale}/admin/post/',
                    'method'     => 'GET',
                    'controller' => 'App\Controller\Admin\BlogController::index',
                    'host'       => 'ANY'
                ],
                'admin_post_index' => [
                    'path'       => '/{_locale}/admin/post/',
                    'method'     => 'GET',
                    'controller' => 'App\Controller\Admin\BlogController::index',
                    'host'       => 'ANY'
                ],
                'admin_post_new' => [
                    'path'       => '/{_locale}/admin/post/new',
                    'method'     => 'GET|POST',
                    'controller' => 'App\Controller\Admin\BlogController::new',
                    'host'       => 'ANY'
                ],
            ],
            $this->analyzer->analyze($this->fullProject)['route_collection']
        );
    }

    // public function testEmptyProject(): void
    // see Testcase.php
}
