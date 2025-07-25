<?php

namespace Tests\Unit;

use LogicException;
use PerryRylance\StreamingJsonConsumer\Consumers\Consumer;
use PerryRylance\StreamingJsonConsumer\Consumers\ConsumerFactory;
use PerryRylance\StreamingJsonConsumer\Consumers\ObjectConsumer;
use PerryRylance\StreamingJsonConsumer\Consumers\ArrayConsumer;
use PerryRylance\StreamingJsonConsumer\Consumers\ConstantConsumer;
use PerryRylance\StreamingJsonConsumer\Consumers\NumberConsumer;
use PerryRylance\StreamingJsonConsumer\Consumers\StringConsumer;
use PerryRylance\StreamingJsonConsumer\Exceptions\ConsumeException;
use Tests\TestCase;

class ScalarTest extends ConsumeTestCase
{
    public function testConsumeNull(): void
    {
        $this->testConsumesAllData('null', ConstantConsumer::class);
    }

    public function testConsumeFalse(): void
    {
        $this->testConsumesAllData('false', ConstantConsumer::class);
    }

    public function testConsumeTrue(): void
    {
        $this->testConsumesAllData('true', ConstantConsumer::class);
    }

    public function testThrowsConsumingInvalidConstant(): void
    {
        $this->testConsumeThrows('nuln', ConstantConsumer::class);
    }

    public function testIntegers(): void
    {
        $this->testConsumesAllData("0", NumberConsumer::class);
        $this->testConsumesAllData("1", NumberConsumer::class);
        $this->testConsumesAllData("123456789", NumberConsumer::class);
        $this->testConsumesAllData("-1", NumberConsumer::class);
        $this->testConsumesAllData("-987654321", NumberConsumer::class);
    }

    public function testDecimals(): void
    {
        $this->testConsumesAllData("0.0", NumberConsumer::class);
        $this->testConsumesAllData("1.0", NumberConsumer::class);
        $this->testConsumesAllData("123.456", NumberConsumer::class);
        $this->testConsumesAllData("0.123", NumberConsumer::class);
        $this->testConsumesAllData("-0.1", NumberConsumer::class);
        $this->testConsumesAllData("-123.456", NumberConsumer::class);
    }

    public function testExponentPositive(): void
    {
        $this->testConsumesAllData("1e1", NumberConsumer::class);
        $this->testConsumesAllData("1e+1", NumberConsumer::class);
        $this->testConsumesAllData("1.5e2", NumberConsumer::class);
        $this->testConsumesAllData("1.5e+2", NumberConsumer::class);
        $this->testConsumesAllData("0e10", NumberConsumer::class);
        $this->testConsumesAllData("6.022e23", NumberConsumer::class);
        $this->testConsumesAllData("7E3", NumberConsumer::class);
    }

    public function testExponentNegative(): void
    {
        $this->testConsumesAllData("1e-1", NumberConsumer::class);
        $this->testConsumesAllData("1.5e-2", NumberConsumer::class);
        $this->testConsumesAllData("3.14e-10", NumberConsumer::class);
        $this->testConsumesAllData("0.0e-0", NumberConsumer::class);
        $this->testConsumesAllData("-1e-2", NumberConsumer::class);
        $this->testConsumesAllData("-1.23e-10", NumberConsumer::class);
    }

    public function testString(): void
    {
        $this->testConsumesAllData('"test"', StringConsumer::class);
    }

    public function testEscapedString(): void
    {
        $this->testConsumesAllData('"test \" escaped"', StringConsumer::class);
    }

    public function testThrowsOnUnterminatedString(): void
    {
        $this->testConsumeThrows('"test', StringConsumer::class);
    }
}
