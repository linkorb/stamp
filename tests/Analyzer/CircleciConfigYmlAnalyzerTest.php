<?php
declare(strict_types=1);

use Stamp\Analyzer\CircleciConfigYmlAnalyzer;

final class CircleciConfigYmlAnalyzerTest extends \AnalyzerTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->analyzer = new CircleciConfigYmlAnalyzer();
    }

    public function testParsingSucceeds(): void
    {
        $this->assertEquals(
            file_get_contents($this->fullProject->getBasePath() . '/.circleci/config.yml'),
            $this->analyzer->analyze($this->fullProject)['.circleci/config.yml']
        );
    }

    // public function testEmptyProject(): void
    // see Testcase.php
}
