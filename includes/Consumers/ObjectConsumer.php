<?php

namespace PerryRylance\StreamingJsonConsumer\Consumers;

use LogicException;
use PerryRylance\StreamingJsonConsumer\Events\KeyValuePairEndEvent;
use PerryRylance\StreamingJsonConsumer\Exceptions\ConsumeException;

class ObjectConsumer extends IterableConsumer
{
    public function consume(): void
    {
        $this->begin();

        $fh = $this->getFileHandle();

        $this->assertReadCharacter($fh, '{');

        $expectations = ['key', 'end'];
        $currentKey = null;

        while(true)
        {
            $chr = $this->peek($fh);
            $position = ftell($fh);

            switch($chr)
            {
                case " ":
                case "\r":
                case "\n":
                case "\t":

                    $consumer = ConsumerFactory::create($this->filename, $this, $position, [WhitespaceConsumer::class]);
                    $consumer->consume();

                    fseek($fh, $consumer->getEnd() + 1);

                    break;

                case '}':

                    if(!in_array('end', $expectations))
                        throw new ConsumeException("Unexpected end of object");

                    $this->end($position);

                    break 2;
                
                case ':':

                    if(!in_array('key-value-separator', $expectations))
                        throw new ConsumeException("Unexpected character '$chr' at offset $position");

                    fseek($fh, 1, SEEK_CUR);

                    $expectations = ['value'];

                    break;

                case ',':

                    if(!in_array('pair-separator', $expectations))
                        throw new ConsumeException("Unexpected character '$chr' at offset $position");

                    fseek($fh, 1, SEEK_CUR);

                    $expectations = ['key'];

                    break;

                default:

                    if(in_array('key', $expectations))
                    {
                        $consumer = ConsumerFactory::create($this->filename, $this, $position, [StringConsumer::class]);
                        $consumer->consume();

                        $currentKey = trim($consumer->stringify(), '"');

                        fseek($fh, $consumer->getEnd() + 1);

                        if($consumer instanceof StringConsumer)
                            $expectations = ['key-value-separator'];
                    }
                    else if(in_array('value', $expectations))
                    {
                        $consumer = ConsumerFactory::create($this->filename, $this, $position);
                        $consumer->consume();

                        if($currentKey === null)
                            throw new LogicException();

                        $this->dispatch(new KeyValuePairEndEvent($currentKey, $consumer));

                        fseek($fh, $consumer->getEnd() + 1);

                        if(!($consumer instanceof WhitespaceConsumer))
                        {
                            $expectations = ['pair-separator', 'end'];
                            $currentKey = null;
                        }
                    }
                    else
                        throw new ConsumeException("Unexpected character '$chr' at offset $position");

                    break;
            }
        }

        fclose($fh);
    }
}
