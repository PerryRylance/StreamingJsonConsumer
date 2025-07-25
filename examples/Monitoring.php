<?php

use Carbon\CarbonInterval;
use PerryRylance\StreamingJsonConsumer\Consumers\DocumentConsumer;
use PerryRylance\StreamingJsonConsumer\Events\EndEvent;

function getFriendlySize($size)
{
    $unit = array('b','kb','mb','gb','tb','pb');
    return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
}

function getFriendlyElapsed($seconds)
{
    return CarbonInterval::seconds($seconds)->cascade()->forHumans();
}

$document = new DocumentConsumer('examples/cities.geojson');

$previous = 0;
$total = $document->filesize();
$start = time();

$document->on(EndEvent::class, function($event) use ($start, $total, &$previous) {

    $current = $event->consumer->getEnd();
    
    $progress = round(100 * ($current / $total));

    if($progress > $previous)
    {
        $size = memory_get_usage(true);

        $usage = getFriendlySize($size);
        $elapsed = getFriendlyElapsed(time() - $start);

        echo "$progress% ($elapsed / $usage)..." . PHP_EOL;

        $previous = $progress;
    }

});

$document->consume();

$size = getFriendlySize($total);
$elapsed = getFriendlyElapsed(time() - $start);
$peak = getFriendlySize(memory_get_peak_usage(true));

echo PHP_EOL . "Consumed $size in $elapsed with peak memory usage $peak" . PHP_EOL;
