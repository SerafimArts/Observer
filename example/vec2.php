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
$observer->x->subscribe(fn ($val) => $this->x = $val + 1);

$vec2->x = 23;

var_dump($vec2);
