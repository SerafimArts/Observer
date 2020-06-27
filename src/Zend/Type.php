<?php

/**
 * This file is part of Observer package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\Observer\Zend;

use FFI\CData;

/**
 * @link https://github.com/php/php-src/blob/f0f5c415a6e0abc40514f97113deb52a343174ee/Zend/zend_types.h#L411-L438
 */
final class Type
{
    /* regular data types */
    public const IS_UNDEF     = 0;
    public const IS_NULL      = 1;
    public const IS_FALSE     = 2;
    public const IS_TRUE      = 3;
    public const IS_LONG      = 4;
    public const IS_DOUBLE    = 5;
    public const IS_STRING    = 6;
    public const IS_ARRAY     = 7;
    public const IS_OBJECT    = 8;
    public const IS_RESOURCE  = 9;
    public const IS_REFERENCE = 10;

    /* constant expressions */
    public const IS_CONSTANT_AST = 11;

    /* internal types */
    public const IS_INDIRECT  = 13;
    public const IS_PTR       = 14;
    public const IS_ALIAS_PTR = 15;

    /* fake types used only for type hinting (Z_TYPE(zv) can not use them) */
    public const IS_CALLABLE = 17;
    public const IS_ITERABLE = 18;
    public const IS_VOID     = 19;

    /**
     * @param CData $zval
     * @param int $type
     * @return bool
     */
    public static function is(CData $zval, int $type): bool
    {
        return self::get($zval) === $type;
    }

    /**
     * @param CData $zval
     * @return int
     * @noinspection PhpUndefinedFieldInspection
     */
    public static function get(CData $zval): int
    {
        return $zval->u1->v->type;
    }
}
