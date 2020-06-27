<?php
use \Serafim\Observer\Observer;

require __DIR__ . '/../vendor/autoload.php';

class Vector2
{
    public float $x = 0.0;
    public float $y = 0.0;
}

$vec2 = new Vector2();

$observer = Observer::create($vec2);

$observer->x
    ->subscribe(fn ($to, $from) => print "vec2->x changed from $from to $to\n");

$observer->y
    ->subscribe(fn ($to, $from) => print "vec2->y changed from $from to $to\n")
    ->once();

// first
$vec2->x = 1;
$vec2->y = 1;

// second
$vec2->x = 2;
$vec2->y = 2;
