<?php

namespace PerryRylance\StreamingJsonConsumer\Traits;

use PerryRylance\StreamingJsonConsumer\Events\Event;
use PerryRylance\StreamingJsonConsumer\Traits\ChecksTraits;
use PerryRylance\StreamingJsonConsumer\Traits\ObservesEvents;

trait DispatchesEvents
{
    protected function dispatch(Event $event): void
    {
        $target = $this;

        do{

            if(method_exists($target, 'fire'))
                $target->fire($event);

            $target = $target->parent;

        }while($target !== null);
    }
}
