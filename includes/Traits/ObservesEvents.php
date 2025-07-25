<?php

namespace PerryRylance\StreamingJsonConsumer\Traits;

use LogicException;
use PerryRylance\StreamingJsonConsumer\Events\Event;
use PerryRylance\StreamingJsonConsumer\Exceptions\ObserveException;
use ReflectionClass;

trait ObservesEvents
{
    private array $listeners = [];

    public function on(string $class, callable $callback): void
    {
        $reflection = new ReflectionClass($class);

        if(!$reflection->isSubclassOf(Event::class))
            throw new LogicException('Expected a subclass of ' . Event::class);

        if(!isset($this->listeners[$class]))
            $this->listeners[$class] = [];

        $this->listeners[$class] []= $callback;
    }

    public function off(string $class, callable $callback): void
    {
        if(!isset($this->listeners[$class]))
            throw new ObserveException("No listeners bound to $class");

        if(($index = array_search($callback, $this->listeners[$class])) === false)
            throw new ObserveException("Specified listener not found for $class");

        array_splice($this->listeners, $index, 1);
    }

    public function fire(Event $event): void
    {
        $class = get_class($event);

        if(!isset($this->listeners[$class]))
            return;

        foreach($this->listeners[$class] as $listener)
            $listener($event);
    }
}
