<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../MetaLens.php';

class MetaLensTest extends TestCase
{
    private string $testImagePath;

    protected function setUp(): void
    {
        $this->testImagePath = __DIR__ . '/images/test.jpg';

        if (!file_exists($this->testImagePath)) {
            $this->fail("Test image does not exist at: {$this->testImagePath}");
        }
    }

    public function testAcceptsFilePath()
    {
        $meta = new MetaLens($this->testImagePath);
        $this->assertEquals($this->testImagePath, $meta->getImage());
    }

    public function testAcceptsFilesArray()
    {

        $fakeFile = [
            'tmp_name' => $this->testImagePath
        ];

        $meta = new MetaLens($fakeFile);
        $this->assertEquals($this->testImagePath, $meta->getImage());
    }

    public function testRejectsInvalidInput()
    {
        $this->expectException(InvalidArgumentException::class);
        new MetaLens(123); // Invalid type
    }

    public function testRejectsArrayWithoutTmpName()
    {
        $this->expectException(InvalidArgumentException::class);
        new MetaLens(['name' => 'test.jpg']);
    }

    public function testThrowsExceptionWhenFileNotFound()
    {
        $this->expectException(InvalidArgumentException::class);
        $meta = new MetaLens('nonexistent.jpg');
        $meta->readMetadata(); // triggers validateImage
    }



   

    public function testGetGPSDataReturnsArray()
    {
        $meta = new MetaLens($this->testImagePath);
        $data = $meta->getGPS();

        $this->assertIsArray($data);
    }
    public function testGetGPSDataReturnsNullIfNoGPS()
    {
        $meta = new MetaLens($this->testImagePath);
        $data = $meta->getGPS();

        $this->assertNull($data);
    }

    public function testReadMetadataReturnsArray()
    {
        $meta = new MetaLens($this->testImagePath);
        $data = $meta->readMetadata();

        $this->assertIsArray($data);
    }
}
