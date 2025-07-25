<?php

namespace PerryRylance\StreamingJsonConsumer\Consumers;

use PerryRylance\StreamingJsonConsumer\Exceptions\ConsumeException;

class ConstantConsumer extends Consumer
{
    public function consume(): void
    {
        $this->begin();

        $fh = $this->getFileHandle();
        $position = ftell($fh);
        $chr = $this->peek($fh);

        switch($chr)
        {
            case 'n':
            case 't':
            case 'f':

                $length = $chr === 'f' ? 5 : 4;
                $peek = fread($fh, $length);

                switch($peek)
                {
                    case 'null':
                    case 'true':
                    case 'false':
                        break;
                    
                    default:
                        throw new ConsumeException("Unexpected character '{$peek[1]}' at offset $position");
                }

                $this->end($position + $length - 1);

                break;
            
            default:
                throw new ConsumeException("Unexpected character '$chr' at offset $position");
        }
    }
}
