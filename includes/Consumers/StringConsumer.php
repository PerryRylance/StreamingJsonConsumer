<?php

namespace PerryRylance\StreamingJsonConsumer\Consumers;

use PerryRylance\StreamingJsonConsumer\Exceptions\ConsumeException;

class StringConsumer extends Consumer
{
    public function consume(): void
    {
        $this->begin();

        $fh = $this->getFileHandle();
        $escaped = false;

        $this->assertReadCharacter($fh, '"');

        while(true)
        {
            $chr = $this->getNextCharacter($fh);

            if($chr === '\\')
                $escaped = true;
            else if($chr === '"' && !$escaped)
                break;
            else
                $escaped = false;
        }

        $this->end(ftell($fh) - 1);

        fclose($fh);
    }
}
