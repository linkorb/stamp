<?php
declare(strict_types=1);

use Stamp\Analyzer\PackageJsonAnalyzer;

final class PackageJsonAnalyzerTest extends \AnalyzerTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->analyzer = new PackageJsonAnalyzer();
    }

    public function testParsingSucceeds(): void
    {
        $this->assertEquals(
            array(
                "name" => "example-react",
                "version" => "0.1.0",
                "private" => true,
                "dependencies" => array(
                    "react" => "^16.4.2",
                    "react-dom" => "^16.4.2",
                    "react-scripts" => "1.1.4"
                ),
                "scripts" => array(
                    "start" => "react-scripts start",
                    "build" => "react-scripts build",
                    "test"  => "react-scripts test --env=jsdom",
                    "eject" => "react-scripts eject"
                )
            ),
            $this->analyzer->analyze($this->fullProject)['package.json']
        );
    }

    // public function testEmptyProject(): void
    // see Testcase.php
}
