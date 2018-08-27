<?php
declare(strict_types=1);

use Stamp\Analyzer\FixturesAnalyzer;

final class FixturesAnalyzerTest extends \TestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->analyzer = new FixturesAnalyzer();
    }

    public function testParsingSucceeds(): void
    {
        $this->assertTrue(
            $this->analyzer->analyze($this->fullProject)['fixtures']
        );
    }

    // public function testEmptyProject(): void
    // see Testcase.php
}
