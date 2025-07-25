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
        $consumer = ConsumerFactory::create($this->filename, $this, $this->getBomLength());

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

    private function getBomLength(): int
    {
        $fh = fopen($this->filename, 'r');
        $bom = fread($fh, 4);
        $length = 0;

        if (strncmp($bom, "\x00\x00\xFE\xFF", 4) === 0 || strncmp($bom, "\xFF\xFE\x00\x00", 4) === 0)
            $length = 4; // NB: UTF-32
        else if (strncmp($bom, "\xEF\xBB\xBF", 3) === 0)
            $length = 3; // NB: UTF-8
        else if (strncmp($bom, "\xFE\xFF", 2) === 0)
            $length = 2; // NB: UTF-16 BE
        else if (strncmp($bom, "\xFF\xFE", 2) === 0)
            $length = 2; // NB: UTF-16 LE

        fclose($fh);

        return $length;
    }
}
