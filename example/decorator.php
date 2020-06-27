<?php

/**
 * This file is part of Observer package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Serafim\Observer\Observer;
use Serafim\Observer\ObserverInterface;

require __DIR__ . '/../vendor/autoload.php';

class Listener
{
    public static function listen(object $object): ObserverInterface
    {
        $observer = Observer::create($object);

        foreach (\get_object_vars($object) as $prop => $val) {
            $observer->subscribe($prop, function ($v, $o) use ($prop) {
                echo ' - ' . \get_class($this) . "::$$prop = $v; // from $o\n";
            });
        }

        return $observer;
    }
}

///

class Vector2
{
    public float $x = 0.0;
    public float $y = 0.0;

    public function __construct(float $x = 0.0, float $y = 0.0)
    {
        [$this->x, $this->y] = [$x, $y];
    }

    public function add(Vector2 $vec2): self
    {
        [$this->x, $this->y] = [
            $this->x + $vec2->x,
            $this->y + $vec2->y,
        ];

        return $this;
    }
}


$vec2 = new Vector2(1, 2);

/** @var Observer|Vector2 $listener */
$listener = Listener::listen($vec2);

$vec2->x = 23;
$vec2->y = 42;
$vec2->add(new Vector2(4, 5));

// Bug: And now it will not be called. DCE or func inlining?
$vec2->add(new Vector2(4, 5));
$vec2->add(new Vector2(4, 5));

