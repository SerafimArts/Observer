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
 * @mixin AmountChangeableInterface
 */
trait AmountChangeableTrait
{
    /**
     * @var int
     */
    protected int $times = AmountChangeableInterface::INFINITE_TIMES;

    /**
     * @return $this|AmountChangeableInterface
     */
    public function once(): AmountChangeableInterface
    {
        return $this->times(1);
    }

    /**
     * @return $this|AmountChangeableInterface
     */
    public function twice(): AmountChangeableInterface
    {
        return $this->times(2);
    }

    /**
     * @param int $times
     * @return $this|AmountChangeableInterface
     */
    public function times(int $times): AmountChangeableInterface
    {
        $this->times = $times;

        return $this;
    }

    /**
     * @return $this|AmountChangeableInterface
     */
    public function endless(): AmountChangeableInterface
    {
        return $this->times(AmountChangeableInterface::INFINITE_TIMES);
    }
}
