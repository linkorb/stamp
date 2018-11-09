<?php
declare(strict_types=1);

use Stamp\Analyzer\BowerJsonAnalyzer;

final class BowerJsonAnalyzerTest extends \AnalyzerTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->analyzer = new BowerJsonAnalyzer();
    }

    public function testParsingSucceeds(): void
    {
        $this->assertEquals(
            array(
                "name" => "myProject",
                "version" => "1.0.0",
                "dependencies" => array(
                    "bootstrap" => "~3.3.0",
                    "bootswatch-dist" => "3.3.0-lumen",
                    "font-awesome" => "~4.3.0",
                    "jquery" => ">= 1.9.1"
                )
            ),
            $this->analyzer->analyze($this->fullProject)['bower.json']
        );
    }

    // public function testEmptyProject(): void
    // see Testcase.php
}
