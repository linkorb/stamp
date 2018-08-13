<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Stamp\Analyzer\DotEnvAnalyzer;

final class DotEnvAnalyzerrTest extends TestCase
{
    private $analyzerResults;

    protected function setUp()
    {
        $analyzer = new DotEnvAnalyzer();
        $dotEnv = file_get_contents(__DIR__ . '/../../example/.env.dist');

        $this->analyzerResults = $analyzer->analyzeDotEnv($dotEnv);
    }

    public function testParsingSucceeds(): void
    {
        $this->assertEquals(
            [
                [
                    "name" => 'APP_ENV',
                    "default" => 'DEBUG',
                    "comment" => "Application environments.\nOptions: DEBUG | PROD"
                ],
                [
                    "name" => 'PDO_URL',
                    "default" => 'mysql://username:password@localhost/dbname',
                    "comment" => 'PDO URL. For example' 
                ],
                [
                    "name" => 'XYZ_TOKEN',
                    "default" =>  'super-secret',
                    "comment" => 'Token to do XYZ'
                ],
            ],
            $this->analyzerResults
        );
    }
}
