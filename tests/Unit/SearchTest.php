<?php

namespace Tests\Unit;

use PerryRylance\StreamingJsonConsumer\Consumers\Consumer;
use PerryRylance\StreamingJsonConsumer\Consumers\DocumentConsumer;
use PerryRylance\StreamingJsonConsumer\Events\EndEvent;
use PerryRylance\StreamingJsonConsumer\Events\KeyValuePairEndEvent;

class SearchTest extends ConsumeTestCase
{
    public function testSearch(): void
    {
        $consumer = new DocumentConsumer('./tests/Assets/search.json');

        $consumer->on(KeyValuePairEndEvent::class, function(KeyValuePairEndEvent $event) {

            if($event->key === 'match' && $event->consumer->stringify() === 'true')
            {
                /** @var IterableConsumer $ancestor */
                $ancestor = $event->consumer->parent->parent;

                $ancestor->on(EndEvent::class, function(EndEvent $event) use ($ancestor) {

                    if($event->consumer !== $ancestor)
                        return;

                    $expected = json_decode('{
                        "label": "target",
                        "goodies": {
                            "match": true
                        },
                        "test": [
                            123,
                            "yes"
                        ]
                    }', true);

                    $this->assertEquals($expected, $ancestor->deserialize());

                });
            }

        });

        $consumer->consume();
    }
}
