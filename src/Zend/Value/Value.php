<?php

/**
 * This file is part of Observer package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\Observer\Zend\Value;

use FFI\CData;
use Serafim\Observer\Zend;

/**
 * Class Value
 */
abstract class Value
{
    /**
     * @var string
     */
    protected const ERROR_CORRUPTED_ARGS_STACK = 'Corrupted stack data';

    /**
     * @var string
     */
    protected const ERROR_CORRUPTED_CALL_STACK = 'Can not resolve passed zend_execute_data from call stack';

    /**
     * @param int $haystack
     * @return bool
     */
    abstract protected static function assertType(int $haystack): bool;

    /**
     * @param CData $value
     * @return mixed
     */
    abstract protected static function getValue(CData $value);

    /**
     * @param Zend $zend
     * @param Mixed $value
     * @return mixed
     *
     * @noinspection PhpUndefinedFieldInspection
     */
    public static function create(Zend $zend, $value)
    {
        $previous = $zend->executor
            ->getExecuteData()
            ->getPrevious()
        ;

        if ($previous === null) {
            throw new \RuntimeException(static::ERROR_CORRUPTED_CALL_STACK);
        }

        $zval = $previous->getArgument(1);

        if (! static::assertType(Zend\Type::get($zval))) {
            throw new \RuntimeException(static::ERROR_CORRUPTED_ARGS_STACK);
        }

        return static::getValue($zval->value);
    }
}
