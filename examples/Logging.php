<?php

use PerryRylance\StreamingJsonConsumer\Consumers\DocumentConsumer;
use PerryRylance\StreamingJsonConsumer\Events\BeginEvent;
use PerryRylance\StreamingJsonConsumer\Events\EndEvent;

$document = new DocumentConsumer('examples/cities.geojson');

// NB: Example - logging start / end
$document->on(BeginEvent::class, fn($event) => printf("Begin %s at offset\t0x%x" . PHP_EOL, get_class($event->consumer), $event->consumer->getStart()));
$document->on(EndEvent::class, fn($event) => printf("End %s at offset\t0x%x" . PHP_EOL, get_class($event->consumer), $event->consumer->getEnd()));

$document->consume();
