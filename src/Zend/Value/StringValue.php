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
 * Class StringValue
 */
final class StringValue extends Value
{
    /**
     * @param int $haystack
     * @return bool
     */
    protected static function assertType(int $haystack): bool
    {
        return $haystack === Zend\Type::IS_STRING;
    }

    /**
     * @param CData $zval
     * @return CData
     *
     * @noinspection PhpUndefinedFieldInspection
     */
    protected static function getValue(CData $zval): CData
    {
        return $zval->str[0];
    }
}
