<?php
declare(strict_types=1);

use Stamp\Analyzer\MakefileAnalyzer;

final class MakefileAnalyzerTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->analyzer = new MakefileAnalyzer();
    }

    public function testParsingSucceeds(): void
    {
        $this->assertEquals(
            [
                'clean' => 'Clean working directory',
                '.env' => null,
                'build' => 'Build it',
                'composer.lock' => 'Generate composer.lock',
                'vendor' => null,
                'test' => null,
                'phpqa-phpcs' => null,
                'help' => 'This help message'
            ],
            $this->analyzer->analyze($this->fullProject)['Makefile']
        );
    }
}
