<?php

namespace Tests\Unit;

use LogicException;
use PerryRylance\StreamingJsonConsumer\Consumers\ArrayConsumer;
use PerryRylance\StreamingJsonConsumer\Consumers\ObjectConsumer;

class IterableTest extends ConsumeTestCase
{
    private function testEmptyIterable(string $class, ?int $whitespace = 0): void
    {
        $inner = str_repeat(' ', $whitespace);

        switch($class)
        {
            case ObjectConsumer::class:
                $data = '{' . $inner . '}';
                break;
            
            case ArrayConsumer::class:
                $data = "[$inner]";
                break;
            
            default:
                throw new LogicException();
        }

        $this->testConsumesAllData($data, $class);
    }

    public function testEmptyArray(): void
    {
        $this->testEmptyIterable(ArrayConsumer::class);
    }

    public function testEmptyArrayWithWhitespace(): void
    {
        $this->testEmptyIterable(ArrayConsumer::class, 3);
    }

    public function testArrayOfScalars(): void
    {
        $this->testConsumesAllData("[123, 234, 345]", ArrayConsumer::class);
    }

    public function testThrowsOnMalformedArray(): void
    {
        $this->testConsumeThrows("[123,,234]", ArrayConsumer::class);
    }

    public function testThrowsOnUnterminatedArray(): void
    {
        $this->testConsumeThrows("[123,234", ArrayConsumer::class);
    }

    public function testEmptyObject(): void
    {
        $this->testEmptyIterable(ObjectConsumer::class);
    }

    public function testEmptyObjectWithWhitespace(): void
    {
        $this->testEmptyIterable(ObjectConsumer::class, 3);
    }

    public function testObjectWithKeyAndScalarValue(): void
    {
        $this->testConsumesAllData('{"test": 123}', ObjectConsumer::class);
    }

    public function testObjectWithIterableValue(): void
    {
        $this->testConsumesAllData('{"test": [123, 234, 345]}', ObjectConsumer::class);
    }

    public function testThrowsOnObjectWithNoValue(): void
    {
        $this->testConsumeThrows('{"test"}', ObjectConsumer::class);
    }

    public function testThrowsOnObjectWithInvalidKey(): void
    {
        $this->testConsumeThrows('{0: "test"}', ObjectConsumer::class);
    }

    public function testThrowsOnUnterminatedObject(): void
    {
        $this->testConsumeThrows('{"test": "unterminated"', ObjectConsumer::class);
    }
}
