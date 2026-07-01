<?php
declare(strict_types=1);

namespace ControlBit\Dto\Tests\Mapper;

use ControlBit\Dto\Attribute\Transformers\FileBase64;
use ControlBit\Dto\Tests\LibraryTestCase;

class FileBase64TransformerTest extends LibraryTestCase
{
    private const TEST_FILE_PATH = __DIR__.'/../Resources/some-image.jpg';

    public function testFromStringToFile(): void
    {
        $contents       = \file_get_contents(self::TEST_FILE_PATH);
        $base64Contents = 'data:image/jpeg;base64,'.\base64_encode($contents);

        $from = new class($base64Contents) {

            public function __construct(public string $file)
            {
            }
        };

        $to = new class() {
            #[FileBase64(options: ['reverse' => true])]
            public \SplFileInfo $file;
        };

        $this->getMapper()->map($from, $to);

        $this->assertEquals(\file_get_contents($to->file->getRealPath()), $contents);
    }

    public function testFromFileToString(): void
    {
        $file           = new \SplFileInfo(self::TEST_FILE_PATH);
        $contents       = \file_get_contents(self::TEST_FILE_PATH);
        $base64Contents = 'data:image/jpeg;base64,'.\base64_encode($contents);

        $from = new class($file) {
            public function __construct(public \SplFileInfo $file)
            {
            }
        };

        $to = new class() {
            #[FileBase64]
            public string $file;
        };

        $this->getMapper()->map($from, $to);

        $this->assertEquals($to->file, $base64Contents);
    }

    public function testFromStringToFileWithSmallChunk(): void
    {
        $contents       = \file_get_contents(self::TEST_FILE_PATH);
        $base64Contents = 'data:image/jpeg;base64,'.\base64_encode($contents);

        $from = new class($base64Contents) {

            public function __construct(public string $file)
            {
            }
        };

        $to = new class() {
            #[FileBase64(chunkSize: 4, options: ['reverse' => true])]
            public \SplFileInfo $file;
        };

        $this->getMapper()->map($from, $to);

        $this->assertEquals(\file_get_contents($to->file->getRealPath()), $contents);
    }

    public function testFromFileToStringWhenCustomTempDirAndCustomPrefix(): void
    {
        $contents       = \file_get_contents(self::TEST_FILE_PATH);
        $base64Contents = 'data:image/jpeg;base64,'.\base64_encode($contents);

        $from = new class($base64Contents) {

            public function __construct(public string $file)
            {
            }
        };

        $to = new class() {
            #[FileBase64(tempPrefix: 'waldoo',tempDir: __DIR__.'/../../var', options: ['reverse' => true])]
            public \SplFileInfo $file;
        };

        $this->getMapper()->map($from, $to);

        $this->assertStringContainsString('/var', $to->file->getPath());
        $this->assertStringContainsString('waldoo', $to->file->getPathname());
    }
}