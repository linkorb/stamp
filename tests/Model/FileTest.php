<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Stamp\Model\File;

final class FileTest extends TestCase
{
    public function testCreateFileAndExpectExtensions(): void
    {
        $file = new File('example/file/path.html.twig', null, []);

        $this->assertEquals(['html', 'twig'], $file->getExtensions());

        $this->assertTrue($file->hasExtension('html'));
        $this->assertTrue($file->hasExtension('twig'));
        $this->assertFalse($file->hasExtension('php'));
    }
}
