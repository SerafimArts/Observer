<?php

/**
 * This file is part of Observer package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\Observer;

final class Subscription implements SubscriptionInterface
{
    use AmountChangeableTrait;

    /**
     * @var \Closure
     */
    private \Closure $callback;

    /**
     * @var Observable
     */
    private Observable $property;

    /**
     * Subscription constructor.
     *
     * @param Observable $property
     * @param \Closure $callback
     */
    public function __construct(Observable $property, \Closure $callback)
    {
        $this->property = $property;
        $this->callback = $callback;
    }

    /**
     * @param mixed $value
     * @return void
     */
    public function notify($value): void
    {
        $this($value, ($this->property->getter)());
    }

    /**
     * @return void
     */
    public function dispose(): void
    {
        $this->property->unsubscribe($this);
    }

    /**
     * @param mixed $value
     * @param mixed $before
     * @return mixed
     */
    public function __invoke($value, $before)
    {
        try {
            return $this->call($value, $before);
        } finally {
            if ($this->times !== self::INFINITE_TIMES) {
                $this->times--;

                if ($this->times <= 0) {
                    $this->dispose();
                }
            }
        }
    }

    /**
     * @param mixed $value
     * @param mixed $before
     * @return mixed
     */
    private function call($value, $before)
    {
        $args = [$value, $before, $this];

        return ($this->callback)->call($this->property->object, ...$args);
    }

    /**
     * @return array
     */
    public function __debugInfo(): array
    {
        return [
            'property' => $this->property,
            'callback' => $this->callback,
            'times'    => $this->times,
        ];
    }
}
