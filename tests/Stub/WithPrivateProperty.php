<?php

/**
 * This file is part of Observer package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\Observer\Tests\Stub;

class WithPrivateProperty
{
    protected $property;

    public function __set($name, $value)
    {
        $this->property = $value;
    }
}
