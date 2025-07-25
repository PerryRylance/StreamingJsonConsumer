<?php

namespace Tests\Unit;

use PerryRylance\StreamingJsonConsumer\Consumers\ArrayConsumer;
use PerryRylance\StreamingJsonConsumer\Consumers\ObjectConsumer;
use Tests\TestCase;

class DeserializeTest extends ConsumeTestCase
{
    protected function testDeserialize(string $data, string $class): void
    {
        $expected = json_decode($data, true);

        /** @var IterableConsumer $consumer */
        $consumer = parent::testConsume($data, $class);

        $actual = $consumer->deserialize();

        $this->assertEquals($expected, $actual);
    }

    public function testDeserializeArray(): void
    {
        $this->testDeserialize('[1, 2, 3]', ArrayConsumer::class);
    }

    public function testDeserializeObject(): void
    {
        $this->testDeserialize('{"key": 123}', ObjectConsumer::class);
    }
}
