<?php

namespace Tests\Unit;

use PerryRylance\StreamingJsonConsumer\Consumers\ArrayConsumer;
use PerryRylance\StreamingJsonConsumer\Consumers\ConstantConsumer;
use PerryRylance\StreamingJsonConsumer\Consumers\Consumer;
use PerryRylance\StreamingJsonConsumer\Consumers\NumberConsumer;
use PerryRylance\StreamingJsonConsumer\Consumers\ObjectConsumer;
use PerryRylance\StreamingJsonConsumer\Consumers\StringConsumer;

class StringifyTest extends ConsumeTestCase
{
    protected function testConsume(string $data, string $class): Consumer
    {
        $consumer = parent::testConsume($data, $class);

        $this->assertEquals($data, $consumer->stringify());

        return $consumer;
    }

    public function testConstant(): void
    {
        $this->testConsume('true', ConstantConsumer::class);
    }

    public function testNumber(): void
    {
        $this->testConsume('54321', NumberConsumer::class);
    }

    public function testString(): void
    {
        $this->testConsume('"test"', StringConsumer::class);
    }

    public function testArray(): void
    {
        $this->testConsume('[1, 2, 3]', ArrayConsumer::class);
    }

    public function testObject(): void
    {
        $this->testConsume('{"key": "value"}', ObjectConsumer::class);
    }
}
