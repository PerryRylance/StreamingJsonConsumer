<?php

namespace PerryRylance\StreamingJsonConsumer\Events;

use PerryRylance\StreamingJsonConsumer\Consumers\Consumer;

class KeyValuePairEndEvent extends Event
{
    public function __construct(public readonly string $key, public readonly Consumer $consumer) {}
}
