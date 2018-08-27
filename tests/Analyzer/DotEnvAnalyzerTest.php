<?php
declare(strict_types=1);

use Stamp\Analyzer\DotEnvAnalyzer;

final class DotEnvAnalyzerTest extends \TestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->analyzer = new DotEnvAnalyzer();
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
                    "default" =>  '"MTIzNA=="',
                    "comment" => 'Token to do XYZ'
                ],
            ],
            $this->analyzer->analyze($this->fullProject)['.env.dist']
        );
    }

    // public function testEmptyProject(): void
    // see Testcase.php
}
