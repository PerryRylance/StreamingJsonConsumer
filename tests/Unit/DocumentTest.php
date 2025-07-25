<?php

namespace PerryRylance\StreamingJsonConsumer\Consumers;

use PerryRylance\StreamingJsonConsumer\Exceptions\ConsumeException;
use Tests\TestCase;
use Tests\Unit\ConsumeTestCase;

class DocumentTest extends ConsumeTestCase
{
    public function testThrowsOnTrailingData(): void
    {
        $this->expectException(ConsumeException::class);

        $filename = $this->writeTempData('{"test": "trailing"} data');

        $document = new DocumentConsumer($filename);
        $document->consume();
    }

    private function testConsumesBom(string $bom): void
    {
        $this->expectNotToPerformAssertions();

        $filename = $this->writeTempData($bom . '{"test": "byte order marker"}');

        $document = new DocumentConsumer($filename);
        $document->consume();
    }

    public function testConsumesUtf8Bom(): void
    {
        $this->testConsumesBom("\xEF\xBB\xBF");
    }

    public function testConsumesUtf16BeBom(): void
    {
        $this->testConsumesBom("\xFE\xFF");
    }

    public function testConsumesUtf16LeBom(): void
    {
        $this->testConsumesBom("\xFF\xFE");
    }

    public function testConsumesUtf32BeBom(): void
    {
        $this->testConsumesBom("\x00\x00\xFE\xFF");
    }

    public function testConsumesUtf32LeBom(): void
    {
        $this->testConsumesBom("\xFF\xFE\x00\x00");
    }
}
