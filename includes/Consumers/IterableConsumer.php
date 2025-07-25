<?php

namespace PerryRylance\StreamingJsonConsumer\Consumers;

abstract class IterableConsumer extends Consumer
{
    public function deserialize(): iterable
    {
        return json_decode($this->stringify(), true);
    }
}
