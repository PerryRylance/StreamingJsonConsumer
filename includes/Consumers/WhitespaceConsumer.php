<?php

namespace PerryRylance\StreamingJsonConsumer\Consumers;

class WhitespaceConsumer extends Consumer
{
    public function consume(): void
    {
        $this->begin();

        $fh = $this->getFileHandle();

        while(preg_match('/\s/', $this->peek($fh)))
            fseek($fh, 1, SEEK_CUR);

        $this->end(ftell($fh) - 1);

        fclose($fh);
    }
}
