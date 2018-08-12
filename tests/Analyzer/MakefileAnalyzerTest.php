<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Stamp\Analyzer\MakefileAnalyzer;

final class MakefileAnalyzerTest extends TestCase
{
    private $analyzerResults;

    protected function setUp()
    {
        $analyzer = new MakefileAnalyzer();
        $makefile = file_get_contents(__DIR__ . '/../../example/Makefile');

        $this->analyzerResults = $analyzer->analyzeMakefile($makefile);
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
            $this->analyzerResults
        );
    }
}
