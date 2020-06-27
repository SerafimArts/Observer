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
 * Interface FactoryInterface
 */
interface FactoryInterface
{
    /**
     * @param object $object
     * @return ObserverInterface|object
     */
    public static function create(object $object): ObserverInterface;
}
