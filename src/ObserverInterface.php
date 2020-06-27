<?php

/**
 * This file is part of Observer package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\Observer;

interface ObserverInterface
{
    /**
     * @param string $property
     * @param \Closure $then
     * @return SubscriptionInterface
     */
    public function subscribe(string $property, \Closure $then): SubscriptionInterface;
}
