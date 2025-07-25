<?php

use GetOpt\GetOpt;
use GetOpt\Option;

require_once 'vendor/autoload.php';

// NB: Download file to work on
$file = 'examples/cities.geojson';

if (!file_exists($file))
{
    echo "⬇️ Downloading $file... ";
    flush();
    file_put_contents($file, file_get_contents('https://raw.githubusercontent.com/drei01/geojson-world-cities/master/cities.geojson'));
}

// NB: Get CLI args
$getopt = new GetOpt();
$getopt->addOptions([
    Option::create(null, 'file', GetOpt::REQUIRED_ARGUMENT)
        ->setDescription('File to play')
]);

$getopt->process();

if($arg = $getopt->getOption('file'))
    $pattern = $arg;
else
    $pattern = "examples/*.php";

// NB: Play!
foreach(glob($pattern) as $file)
{
    if(preg_match('/bootstrap\.php$/', $file))
        continue;

    echo "🎬 Running $file..." . PHP_EOL . PHP_EOL;

    include $file;

    echo PHP_EOL;
}
