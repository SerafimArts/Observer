<?php

/**
 * This file is part of Observer package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\Observer;

final class Observable implements ObservableInterface
{
    /**
     * @var object
     */
    public object $object;

    /**
     * @var string
     */
    private string $property;

    /**
     * @var \Closure
     */
    public \Closure $getter;

    /**
     * @var \SplObjectStorage|Subscription[]
     */
    private \SplObjectStorage $subscriptions;

    /**
     * PropertyObserver constructor.
     *
     * @param object $object
     * @param string $property
     */
    public function __construct(object $object, string $property)
    {
        $this->subscriptions = new \SplObjectStorage();

        $this->object = $object;
        $this->property = $property;
        $this->getter = $this->createGetter($property);
    }

    /**
     * @param string $property
     * @return \Closure
     */
    private function createGetter(string $property): \Closure
    {
        return function () use ($property) {
            try {
                return $this->$property;
            } catch (\Throwable $e) {
                return null;
            }
        };
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->getter->call($this->object);
    }

    /**
     * {@inheritDoc}
     */
    public function subscribe(\Closure $handler): SubscriptionInterface
    {
        $this->subscriptions->attach(
            $subscription = new Subscription($this, $handler)
        );

        return $subscription;
    }

    /**
     * {@inheritDoc}
     */
    public function unsubscribe(SubscriptionInterface $subscription): void
    {
        $this->subscriptions->detach($subscription);
    }

    /**
     * {@inheritDoc}
     */
    public function unsubscribeAll(): void
    {
        $this->subscriptions->removeAll(
            $this->subscriptions
        );
    }

    /**
     * @return \Traversable|SubscriptionInterface[]
     */
    public function getIterator(): \Traversable
    {
        return $this->subscriptions;
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return $this->subscriptions->count();
    }

    /**
     * {@inheritDoc}
     */
    public function notify($value, $before): void
    {
        foreach ($this->subscriptions as $subscription) {
            $subscription($value, $before);
        }
    }

    /**
     * @return array
     */
    public function __debugInfo(): array
    {
        return [
            'object'   => $this->object,
            'property' => $this->property,
        ];
    }
}
