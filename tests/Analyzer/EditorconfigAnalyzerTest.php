<?php
declare(strict_types=1);

use Stamp\Analyzer\EditorconfigAnalyzer;

final class EditorconfigAnalyzerTest extends \TestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->analyzer = new EditorconfigAnalyzer();
    }

    public function testParsingSucceeds(): void
    {
        $this->assertEquals(
            file_get_contents($this->fullProject->getBasePath() . '/.editorconfig'),
            $this->analyzer->analyze($this->fullProject)['.editorconfig']
        );
    }

    // public function testEmptyProject(): void
    // see Testcase.php
}
