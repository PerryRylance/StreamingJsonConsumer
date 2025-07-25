<?php

namespace PerryRylance\StreamingJsonConsumer\Consumers;

use PerryRylance\StreamingJsonConsumer\Exceptions\ConsumeException;

class ConsumerFactory
{
    public static function create(string $filename, ?Consumer $parent = null, ?int $position = 0, ?array $allowed = []): Consumer
    {
        $fh = fopen($filename, 'r');
        fseek($fh, $position);
        $chr = fgetc($fh);
        fclose($fh);

        switch($chr)
        {
            case '{':
                $result = new ObjectConsumer($filename, $position, $parent);
                break;
            
            case '[':
                $result = new ArrayConsumer($filename, $position, $parent);
                break;

            case ' ':
            case "\r":
            case "\n":
            case "\t":
                $result = new WhitespaceConsumer($filename, $position, $parent);
                break;

            case 'n':
            case 't':
            case 'f':
                $result = new ConstantConsumer($filename, $position, $parent);
                break;

            case '-':
            case '0':
            case '1':
            case '2':
            case '3':
            case '4':
            case '5':
            case '6':
            case '7':
            case '8':
            case '9':
                $result = new NumberConsumer($filename, $position, $parent);
                break;

            case '"':
                $result = new StringConsumer($filename, $position, $parent);
                break;
            
            default:
                throw new ConsumeException("Unexpected character '$chr' at offset $position");
        }

        $class = get_class($result);

        if(!empty($allowed) && !in_array($class, $allowed))
            throw new ConsumeException("$class not allowed at offset $position");

        return $result;
    }
}
