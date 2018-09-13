<?php
declare(strict_types=1);

use Stamp\Analyzer\AnonymizerYmlAnalyzer;

final class AnonymizerYmlAnalyzerTest extends \AnalyzerTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->analyzer = new AnonymizerYmlAnalyzer();
    }

    public function testParsingSucceeds(): void
    {
        $this->assertEquals(
            file_get_contents($this->fullProject->getBasePath() . '/anonymizer.yml'),
            $this->analyzer->analyze($this->fullProject)['anonymizer.yml']
        );
    }

    // public function testEmptyProject(): void
    // see Testcase.php
}
