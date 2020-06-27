<?php

/**
 * This file is part of Observer package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\Observer;

interface AmountChangeableInterface
{
    /**
     * @var int
     */
    public const INFINITE_TIMES = -1;

    /**
     * @return $this
     */
    public function once(): self;

    /**
     * @return $this
     */
    public function twice(): self;

    /**
     * @param int $times
     * @return $this
     */
    public function times(int $times): self;

    /**
     * @return $this
     */
    public function endless(): self;
}
