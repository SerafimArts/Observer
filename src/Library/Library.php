<?php

/**
 * This file is part of Observer package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\Observer\Library;

use Serafim\FFILoader\BitDepth;
use Serafim\FFILoader\Library as BaseLibrary;
use Serafim\FFILoader\OperatingSystem;

/**
 * Class Language
 */
final class Library extends BaseLibrary
{
    /**
     * @var bool
     */
    private bool $debug;

    /**
     * Library constructor.
     *
     * @param bool $debug
     */
    public function __construct(bool $debug = false)
    {
        $this->debug = $debug;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'php-observer';
    }

    /**
     * @return string
     */
    public function getOutputDirectory(): string
    {
        return __DIR__ . '/../../out';
    }

    /**
     * @param string $library
     * @return string
     */
    public function getVersion(string $library): string
    {
        return \PHP_VERSION;
    }

    /**
     * @return string
     */
    public function getHeaders(): string
    {
        return __DIR__ . '/../../resources/php.h';
    }

    /**
     * @param OperatingSystem $os
     * @param BitDepth $bits
     * @return string|null
     */
    public function getLibrary(OperatingSystem $os, BitDepth $bits): ?string
    {
        return $os->isWindows()
            ? \sprintf('php%d.dll', \PHP_MAJOR_VERSION)
            : '';
    }

    /**
     * @return void
     */
    public function __destruct()
    {
        if ($this->debug) {
            foreach (\glob($this->getOutputDirectory() . '/*.h') as $f) {
                \unlink($f);
            }
        }
    }
}
