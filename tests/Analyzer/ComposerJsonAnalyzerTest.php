<?php
declare(strict_types=1);

use Stamp\Analyzer\ComposerJsonAnalyzer;

final class ComposerJsonAnalyzerTest extends \TestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->analyzer = new ComposerJsonAnalyzer();
    }

    public function testParsingSucceeds(): void
    {
        $this->assertEquals(
            array(
                "name" => "acme/example",
                "description" => "Acme Example project",
                "homepage" => "https://github.com/acme/example",
                "keywords" => array("example", "acme"),
                "authors" => array(
                    array(
                    "name" => "Joe Johnson",
                    "email" => "joe@exampl.com",
                    "role" => "Development"
                    )
                ),
                "require" => array(
                    "symfony/console" => "^4.0"
                ),
                "license" => "MIT"
            ),
            $this->analyzer->analyze($this->fullProject)['composer.json']
        );
    }

    // public function testEmptyProject(): void
    // see Testcase.php
}
