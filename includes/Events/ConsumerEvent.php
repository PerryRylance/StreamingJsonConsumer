<?php

namespace PerryRylance\StreamingJsonConsumer\Events;

use PerryRylance\StreamingJsonConsumer\Consumers\Consumer;

class ConsumerEvent extends Event
{
    public function __construct(public readonly Consumer $consumer)
    {
        
    }
}
