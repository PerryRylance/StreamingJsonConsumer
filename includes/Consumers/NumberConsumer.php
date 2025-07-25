<?php

namespace PerryRylance\StreamingJsonConsumer\Consumers;

use PerryRylance\StreamingJsonConsumer\Exceptions\ConsumeException;

class NumberConsumer extends Consumer
{
    public function consume(): void
    {
        $this->begin();

        $fh = $this->getFileHandle();
        $result = '';

        // Read first character
        $position = ftell($fh);
        $char = fread($fh, 1);

        // Optional minus sign
        if ($char === '-')
        {
            $result .= $char;

            $position = ftell($fh);
            $char = fread($fh, 1);
        }

        // First digit must be present
        if ($char === '0')
        {
            $result .= $char;
            $char = fread($fh, 1);
        }
        else if(ctype_digit($char))
        {
            // Non-zero first digit
            while (ctype_digit($char)) {
                $result .= $char;
                $char = fread($fh, 1);
            }
        }
        else
            throw new ConsumeException("Unexpected character '$char' at offset $position");

        // Optional fractional part
        if ($char === '.')
        {
            $result .= $char;

            $position = ftell($fh);
            $char = fread($fh, 1);

            if (!ctype_digit($char))
                throw new ConsumeException("Unexpected character '$char' at offset $position");

            while (ctype_digit($char))
            {
                $result .= $char;
                $char = fread($fh, 1);
            }
        }

        // Optional exponent part
        if ($char === 'e' || $char === 'E') {
            $result .= $char;

            $position = ftell($fh);
            $char = fread($fh, 1);

            if ($char === '+' || $char === '-')
            {
                $result .= $char;
                $char = fread($fh, 1);
            }

            if (!ctype_digit($char))
                throw new ConsumeException("Unexpected character '$char' at offset $position");

            while (ctype_digit($char))
            {
                $result .= $char;
                $char = fread($fh, 1);
            }
        }

        // Unread the non-float character
        if ($char !== false) {
            fseek($fh, -1, SEEK_CUR);
        }

        // TODO: Why not use ftell? Is this going to struggle with unicode?
        $this->end($this->getStart() + strlen($result) - 1);

        fclose($fh);
    }
}
