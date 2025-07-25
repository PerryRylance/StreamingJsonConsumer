<?php

namespace PerryRylance\StreamingJsonConsumer\Consumers;

use LogicException;
use PerryRylance\StreamingJsonConsumer\Exceptions\ConsumeException;

class ArrayConsumer extends IterableConsumer
{
    public function consume(): void
    {
        $this->begin();

        $fh = $this->getFileHandle();

        $expectations = ['value', 'end'];

        $this->assertReadCharacter($fh, '[');

        while(true)
        {
            $position = ftell($fh);
            $chr = $this->peek($fh);

            switch($chr)
            {
                case ']':

                    if(!in_array('end', $expectations))
                        throw new ConsumeException("Unexpected character '$chr' at offset $position");

                    $this->end($position);

                    break 2;

                case ',':

                    if(!in_array('separator', $expectations))
                        throw new ConsumeException("Unexpected character '$chr' at offset $position");

                    fseek($fh, 1, SEEK_CUR);

                    $expectations = ['value'];

                    break;

                default:
                    // TODO: Could actually close the handle here if needed

                    $consumer = ConsumerFactory::create($this->filename, $this, $position);
                    $consumer->consume();

                    $end = $consumer->getEnd();

                    if($end < $position)
                        throw new LogicException();

                    fseek($fh, $end + 1);

                    if(!($consumer instanceof WhitespaceConsumer))
                        $expectations = ['separator', 'end'];

                    break;
            }
        }

        fclose($fh);
    }
}
