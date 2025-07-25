<?php

use PerryRylance\StreamingJsonConsumer\Consumers\DocumentConsumer;
use PerryRylance\StreamingJsonConsumer\Consumers\IterableConsumer;
use PerryRylance\StreamingJsonConsumer\Events\EndEvent;
use PerryRylance\StreamingJsonConsumer\Events\KeyValuePairEndEvent;

function getGreatCircleDistance($lat1, $lon1, $lat2, $lon2, $earthRadius = 6371)
{
    // Convert degrees to radians
    $lat1 = deg2rad($lat1);
    $lon1 = deg2rad($lon1);
    $lat2 = deg2rad($lat2);
    $lon2 = deg2rad($lon2);

    // Haversine formula
    $deltaLat = $lat2 - $lat1;
    $deltaLon = $lon2 - $lon1;

    $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
         cos($lat1) * cos($lat2) *
         sin($deltaLon / 2) * sin($deltaLon / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    // Distance in kilometers by default
    $distance = $earthRadius * $c;

    return $distance;
}

$lat = 36.5298;
$lng = -87.3612;

$closest = (object)[
    'consumer' => null,
    'distance' => INF
];

$lastProgress = 0;

$document = new DocumentConsumer('examples/cities.geojson');

$document->on(KeyValuePairEndEvent::class, function(KeyValuePairEndEvent $event) use ($lat, $lng, $closest, $document, &$lastProgress) {

    $progress = round(100 * ($event->consumer->getEnd() / $document->filesize()));

    if($progress > $lastProgress)
    {
        echo "$progress%..." . PHP_EOL;
        $lastProgress = $progress;
    }

    if($event->key !== 'coordinates')
        return;

    $consumer = $event->consumer;

    if(!($consumer instanceof IterableConsumer))
        throw new LogicException('Unexpected state');

    $polygons = $consumer->deserialize();
    
    foreach($polygons as $coordinates)
    {
        foreach($coordinates as $pair)
        {
            $distance = getGreatCircleDistance($pair[1], $pair[0], $lat, $lng);

            if($distance > $closest->distance)
                continue;

            $closest->distance = $distance;
            $closest->consumer = $consumer;
        }
    }

});

$document->consume();

$feature = $closest->consumer->parent->parent->deserialize();
$pretty  = json_encode( $feature, JSON_PRETTY_PRINT );

echo "The closest feature to $lat, $lng is:" . PHP_EOL . PHP_EOL;
echo $pretty . PHP_EOL;
