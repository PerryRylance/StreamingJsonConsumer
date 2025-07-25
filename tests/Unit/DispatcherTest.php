<?php

namespace Tests\Unit;

use PerryRylance\StreamingJsonConsumer\Consumers\ArrayConsumer;
use PerryRylance\StreamingJsonConsumer\Consumers\ConstantConsumer;
use PerryRylance\StreamingJsonConsumer\Consumers\NumberConsumer;
use PerryRylance\StreamingJsonConsumer\Consumers\ObjectConsumer;
use PerryRylance\StreamingJsonConsumer\Consumers\StringConsumer;
use PerryRylance\StreamingJsonConsumer\Consumers\WhitespaceConsumer;
use PerryRylance\StreamingJsonConsumer\Events\BeginEvent;
use PerryRylance\StreamingJsonConsumer\Events\EndEvent;

class DispatcherTest extends ConsumeTestCase
{
    private function testDispatchesBeginAndEnd(string $class, string $data): void
    {
        $filename = $this->writeTempData($data);
        $consumer = new $class($filename);

        $counts = [
            BeginEvent::class => 0,
            EndEvent::class => 0
        ];

        $consumer->on(BeginEvent::class, function ($event) use ($class, &$counts) {
            if(get_class($event->consumer) == $class)
                $counts[BeginEvent::class]++;
        });

        $consumer->on(EndEvent::class, function ($event) use ($class, &$counts) {
            if(get_class($event->consumer) == $class)
                $counts[EndEvent::class]++;
        });

        $consumer->consume();

        $this->assertEquals(1, $counts[BeginEvent::class], "Expected $class to dispatch " . BeginEvent::class);
        $this->assertEquals(1, $counts[EndEvent::class], "Expected $class to dispatch " . EndEvent::class);
    }

    public function testConstantConsumerDispatchesEvents(): void
    {
        $this->testDispatchesBeginAndEnd(ConstantConsumer::class, 'null');
    }

    public function testWhitespaceConsumerDispatchesEvents(): void
    {
        $this->testDispatchesBeginAndEnd(WhitespaceConsumer::class, '   ');
    }

    public function testNumberConsumerDispatchesEvents(): void
    {
        $this->testDispatchesBeginAndEnd(NumberConsumer::class, '12345');
    }

    public function testStringConsumerDispatchesEvents(): void
    {
        $this->testDispatchesBeginAndEnd(StringConsumer::class, '"test"');
    }

    public function testArrayConsumerDispatchesEvents(): void
    {
        $this->testDispatchesBeginAndEnd(ArrayConsumer::class, '[123, 234]');
    }

    public function testObjectConsumerDispatchesEvents(): void
    {
        $this->testDispatchesBeginAndEnd(ObjectConsumer::class, '{"test": 123}');
    }

    // TODO: Test events bubble
}
