<?php
declare(strict_types=1);

use Stamp\Analyzer\DockerfileAnalyzer;

final class DockerfileAnalyzerTest extends \TestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->analyzer = new DockerfileAnalyzer();
    }

    public function testParsingSucceeds(): void
    {
        $this->assertEquals(
            file_get_contents($this->fullProject->getBasePath() . '/Dockerfile'),
            $this->analyzer->analyze($this->fullProject)['Dockerfile']
        );
    }

    // public function testEmptyProject(): void
    // see Testcase.php
}
