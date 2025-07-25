<?php

namespace PerryRylance\StreamingJsonConsumer\Consumers;

use PerryRylance\StreamingJsonConsumer\Exceptions\ConsumeException;

class DocumentConsumer extends Consumer
{
    public function __construct(string $filename)
    {
        parent::__construct($filename);
    }

    public function filesize(): int
    {
        return filesize($this->filename);
    }

    public function consume(): void
    {
        // TODO: Consume BOM

        $consumer = ConsumerFactory::create($this->filename, $this);

        if(!($consumer instanceof IterableConsumer))
            throw new ConsumeException('Expected iterable root');

        $consumer->consume();

        $fh = $this->getFileHandle();
        $filesize = filesize($this->filename);
        $end = $consumer->getEnd() + 1;
        $trailing = $filesize - $end;

        fseek($fh, $end);

        for($i = 0; $i < $trailing; $i++)
        {
            $chr = fread($fh, 1);

            switch($chr)
            {
                case " ":
                case "\r":
                case "\n":
                case "\t":
                    break;

                default:
                    $position = $end + $i;
                    throw new ConsumeException("Unexpected trailing character '$chr' at offset $position");
            }
        }
    }
}
