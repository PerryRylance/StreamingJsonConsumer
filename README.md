# StreamingJsonConsumer
This package is for both streaming and traversing very large JSON datasets, and has JSONPath to help navigate and debug.

- Traversing huge datasets in memory
- Extracting data with a low memory footprint
- Developing and debugging applications reading huge datasets

This package is not intended for continuously streaming JSON eg over a network - it's intended for large files.

This library can consume the example 20mb `cities.geojson` file in roughly 1m 15s and uses 6mb memory* (PHP 8.3 CLI, XDebug off, 13th Gen Intel Core i7-13850HX, streaming from SSD)

<sub>* - The examples load dev dependencies, in production this should be slimmer</sub>

## Installation
`composer require perry-rylance/streaming-json-parser` to start using in your own projects.

## Usage
To consume a JSON document, you can do the following:

```
use PerryRylance\StreamingJsonConsumer\DocumentConsumer;

$consumer = new DocumentConsumer('filename.json');
$consumer->consume();
```

This won't do anything except walk through the file, you'll probably want to bind some listeners using `on`.

See `/examples` for some examples, the `Logging.php` example is the simplest, it prints the start and end of fragments of JSON as they are discovered.

Once a consumer has reached it's end then you can obtain data using `stringify` or `deserialize`.

## Examples
- You'll need PHP >= 8.3
- Clone this repostory
- `composer install`
- `composer run examples`

To run specific examples you can `composer run examples -- --file=examples/Logging.php`.

## Testing
- You'll need PHP >= 8.3
- Clone this repository
- `composer install`
- `composer run tests`

## Troubleshooting
I wrote this library to process output from CLANG for a personal project - it's almost certainly not comprehensive, please feel free to open PR's or log issues and I will fix them as soon as I can.
