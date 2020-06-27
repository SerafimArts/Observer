# Observer

Please note that this #@%^^@#$ (library) is made for educational purposes. 
Do not use it in real projects if your health is dear to you.

Peace :3

## Inspired By

- [lisachenko/z-engine](https://github.com/lisachenko/z-engine)
- [knockout](https://github.com/knockout/knockout)

## System Requirements

- PHP NTS >= 7.4
- ext-ffi

## Installation

I am too lazy to upload this library on the packagist and produce entropy. 
Therefore, you will have to press the "download" button yourself.

Thank you!

## Usage

For example, we have the following code:

```php
class Vector2
{
    public float $x = 0.0;
    public float $y = 0.0;
}

$vec2 = new Vector2();
```

Now we want to hang certain behavior on it when 
changing fields.

```php
use \Serafim\Observer\Observer;

$observer = Observer::create($vec2);
$observer->x->subscribe(fn ($to, $from) => print "vec2->x changed from $from to $to\n");
$observer->y->subscribe(fn ($to, $from) => print "vec2->y changed from $from to $to\n");

$vec2->x = 42;
// "vec2->x changed from 0 to 42"

$vec2->y = 23;
// "vec2->y changed from 0 to 23"
```

## API

### New Observer Context

```php
$observer = Observer::create($anyObject);
```

### Create Subscription

```php
$subscription = $observer->field_name
    ->subscribe(function ($value, $oldValue) {
        var_dump('changed in object: ', $this);
        var_dump('old value: ' . $oldValue);
        var_dump('new value: ' . $value);
    });

// OR $observer->subscribe('field_name', $callback);
```

### Delete Subscription

```php
$subscription = $observer->field_name->subscribe($callback);

$subscription->dispose();
```

Or alternative method

```php
$subscription = $observer->field_name->subscribe($callback);

$observer->unsubscribe($subscription);
```

### Delete All Subscriptions

```php
$observer->field_name->unsubscribeAll();
```

### Quantity Management

```php
$subscription = $observer->field_name->subscribe($callback);

$subscription->endless();  // [DEFAULT] Subscription will be called every time the value is changed
$subscription->once();     // The subscription will be called ONCE and then deleted
$subscription->twice();    // The subscription will be called TWICE and then deleted
$subscription->times(xxx); // The subscription will be called XXX TIMES and then deleted
```

### Notifications

```php
$subscription = $observer->field_name
    ->subscribe(fn ($val) => print "value is $val\n");

$subscription->notify(42);
// "value is 42"

$subscription->notify(23);
// "value is 23"
```

## Recursive Calls

```php
$observer = Observer::create($vec2);
$observer->x->subscribe(fn ($val) => $this->x = $val);

$vec2->x = 23;

var_dump($vec2);
// object(Vector2)#3 (2) {
//   ["x"]=>
//   float(23)
//   ["y"]=>
//   float(0)
// }
```

However, do not do as follows

```php
$observer = Observer::create($vec2);
$observer->x->subscribe(fn ($val) => $this->x = $val + 1);
                                             // ^^^^^^^^^

$vec2->x = 23;
// Fatal error: Allowed memory size of 134217728 bytes exhausted (tried to allocate 4194304 bytes)
```

## Known Issues

1) Doesn't work in PHP ZTS mode.

2) Unstable behavior on PHP 8.0+

3) Discloses fields encapsulation
```php
$test = new Example();

Observer::create($test)
    ->privateProperty
    ->subscribe(fn (...) => ...);

$test->privateProperty = 23; // No error occurs
```

4) Breaks `__set` "magic" method (It just doesn't work).

