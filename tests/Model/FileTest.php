<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Stamp\Model\File;

final class FileTest extends TestCase
{
    public function testCreateFileAndExpectExtensions(): void
    {
        $file = new File('path.php', 'example/file/path.html.twig', []);

        $this->assertEquals(['html', 'twig'], $file->getTemplateExtensions());

        $this->assertTrue($file->hasTemplateExtension('html'));
        $this->assertTrue($file->hasTemplateExtension('twig'));
        $this->assertFalse($file->hasTemplateExtension('php'));
    }
}
