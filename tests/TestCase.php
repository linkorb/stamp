<?php

require_once __dir__ . '/../vendor/autoload.php';

use Stamp\Loader\YamlProjectLoader;

class TestCase extends PHPUnit\Framework\TestCase {
    protected $fullProject;
    protected $emptyProject;

    protected function setUp() {
        $loader = new YamlProjectLoader();
        $this->fullProject  = $loader->loadFile(__dir__ . '/../examples/full-project/stamp.yml');
        $this->emptyProject = $loader->loadFile(__dir__ . '/../examples/empty-project/stamp.yml');
    }

    public function testEmptyProject(): void
    {
        $this->assertNull(
            $this->analyzer->analyze($this->emptyProject)
        );
    }
}
