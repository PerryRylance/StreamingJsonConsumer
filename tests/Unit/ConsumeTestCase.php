<?php

namespace Tests\Unit;

use PerryRylance\StreamingJsonConsumer\Consumers\Consumer;
use PerryRylance\StreamingJsonConsumer\Consumers\ConsumerFactory;
use PerryRylance\StreamingJsonConsumer\Exceptions\ConsumeException;
use Tests\TestCase;

class ConsumeTestCase extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        foreach(glob('./temp/temp-*') as $filename)
            unlink($filename);
    }

    protected function assertAllDataConsumed(Consumer $consumer, string $data): void
    {
        $this->assertEquals(0, $consumer->getStart());
        $this->assertEquals(strlen($data) - 1, $consumer->getEnd());
    }

    protected function writeTempData(string $data): string
    {
        $filename = tempnam('./temp', 'temp-');

        $fh = fopen($filename, 'w');
        fwrite($fh, $data);
        fclose($fh);

        return $filename;
    }

    protected function testConsume(string $data, string $class): Consumer
    {
        $filename = $this->writeTempData($data);

        $consumer = ConsumerFactory::create($filename);

        $this->assertInstanceOf($class, $consumer);

        $consumer->consume();

        return $consumer;
    }

    protected function testConsumesAllData(string $data, string $class): void
    {
        $consumer = $this->testConsume($data, $class);

        $this->assertAllDataConsumed($consumer, $data);
    }

    protected function testConsumeThrows(string $data, string $class): void
    {
        $this->expectException(ConsumeException::class);

        $this->testConsume($data, $class);
    }
}
