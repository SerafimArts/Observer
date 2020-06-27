<?php

/**
 * This file is part of Observer package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\Observer;

/**
 * Interface SubscriptionInterface
 */
interface SubscriptionInterface extends AmountChangeableInterface
{
    /**
     * @param mixed $value
     * @return void
     */
    public function notify($value): void;

    /**
     * @return void
     */
    public function dispose(): void;
}
