<?php

namespace PerryRylance\StreamingJsonConsumer\Consumers;

use PerryRylance\StreamingJsonConsumer\Events\BeginEvent;
use PerryRylance\StreamingJsonConsumer\Events\EndEvent;
use PerryRylance\StreamingJsonConsumer\Exceptions\ConsumeException;
use PerryRylance\StreamingJsonConsumer\Exceptions\DeserializeException;
use PerryRylance\StreamingJsonConsumer\Traits\DispatchesEvents;
use PerryRylance\StreamingJsonConsumer\Traits\ObservesEvents;

abstract class Consumer
{
    use DispatchesEvents, ObservesEvents;

    private ?int $end = null;

    public function __construct(protected readonly string $filename, private int $start = 0, public readonly ?Consumer $parent = null)
    {
    }

    abstract public function consume(): void;

    public function getStart(): int
    {
        return $this->start;
    }

    public function getEnd(): int | null
    {
        return $this->end;
    }

    public function stringify(): string
    {
        if($this->end === null)
            throw new DeserializeException('Must be fully consumed to stringify');

        $fh = fopen($this->filename, 'r');
        fseek($fh, $this->start);
        $result = fread($fh, ($this->end - $this->start) + 1);

        fclose($fh);

        return $result;
    }

    protected function begin(): void
    {
        // NB: Start already set in constructor - not a great design but works
        $this->dispatch(new BeginEvent($this));
    }

    protected function end(int $position): void
    {
        $this->end = $position;

        $this->dispatch(new EndEvent($this));
    }

    protected function peek($fh, int $length = 1): string
    {
        $result = fread($fh, $length);
        fseek($fh, -$length, SEEK_CUR);
        return $result;
    }

    final protected function getFileHandle()
    {
        $fh = fopen($this->filename, 'r');
        fseek($fh, $this->start);
        return $fh;
    }

    final protected function getNextCharacter($fh): string
    {
        if(feof($fh))
            throw new ConsumeException('Unexpected end of file');

        $chr = fread($fh, 1);

        return $chr;
    }

    final protected function assertReadCharacter($fh, string $expected): void
    {
        $chr = fread($fh, 1);

        if($chr !== $expected)
            throw new ConsumeException("Unexpected character '$chr'");
    }
}
