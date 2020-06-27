<?php

/**
 * This file is part of Observer package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\Observer;

interface ObservableInterface extends \IteratorAggregate, \Countable
{
    /**
     * @param \Closure $handler
     * @return SubscriptionInterface
     */
    public function subscribe(\Closure $handler): SubscriptionInterface;

    /**
     * @param SubscriptionInterface $subscription
     * @return void
     */
    public function unsubscribe(SubscriptionInterface $subscription): void;

    /**
     * @return void
     */
    public function unsubscribeAll(): void;

    /**
     * @param mixed $value
     * @param mixed $before
     * @return mixed
     */
    public function notify($value, $before): void;
}
