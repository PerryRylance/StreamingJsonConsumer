# StreamingJsonConsumer
This package is for both streaming and traversing very large JSON datasets, and has JSONPath to help navigate and debug.

This package is suitable for:

- Traversing huge datasets in memory
- Extracting data
- Developing and debugging applications reading huge datasets

This package is not intended for

- Navigating through objects with a colossal amount of key/value pairs or arrays with huge numbers of elements
- Traversing extremely deeply nested JSON
- Continuously streaming JSON

## Installation
`composer require perry-rylance/streaming-json-parser`

## Usage
```
use PerryRylance\StreamingJsonConsumer\DocumentParser;

$parser = new DocumentParser('filename.json');
$parser->consume();
```
