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
use Serafim\Observer\Zend;

final class Macros
{
    /**
     * Note: It may depend on the compiler settings, however in most cases it
     * contains the value 8.
     *
     * <code>
     *  #ifndef ZEND_MM_ALIGNMENT
     *      #define ZEND_MM_ALIGNMENT Z_UL(8)
     *  #elif ZEND_MM_ALIGNMENT < 4
     *      #define ZEND_MM_ALIGNMENT Z_UL(4)
     *  #endif
     * </code>
     *
     * @link https://github.com/php/php-src/blob/f0f5c415a6e0abc40514f97113deb52a343174ee/Zend/zend_alloc.h#L29-L37
     * @var int
     */
    public const ZEND_MM_ALIGNMENT = 8;

    /**
     * <code>
     *  #define ZEND_MM_ALIGNMENT_MASK ~(ZEND_MM_ALIGNMENT - 1)
     * </code>
     *
     * @link https://github.com/php/php-src/blob/f0f5c415a6e0abc40514f97113deb52a343174ee/Zend/zend_alloc.h#L39
     * @var int
     */
    public const ZEND_MM_ALIGNMENT_MASK = ~(self::ZEND_MM_ALIGNMENT - 1);

    /**
     * @var array
     */
    private array $alignments = [];

    /**
     * @var Zend
     */
    private Zend $zend;

    /**
     * Macro constructor.
     *
     * @param Zend $zend
     */
    public function __construct(Zend $zend)
    {
        $this->zend = $zend;
    }

    /**
     * Macro implementation of zend_compile:ZEND_CALL_FRAME_SLOT
     *
     * <code>
     *  #define ZEND_CALL_FRAME_SLOT (int)(
     *      (
     *          ZEND_MM_ALIGNED_SIZE(
     *              sizeof(zend_execute_data)
     *          ) +
     *          ZEND_MM_ALIGNED_SIZE(
     *              sizeof(zval)
     *          ) - 1
     *      ) / ZEND_MM_ALIGNED_SIZE(
     *          sizeof(zval)
     *      )
     *  )
     * </code>
     *
     * @link https://github.com/php/php-src/blob/f0f5c415a6e0abc40514f97113deb52a343174ee/Zend/zend_compile.h#L564-L565
     */
    public function getCallFrameSlot(): int
    {
        static $callFrameSlot;

        if ($callFrameSlot === null) {
            [$zendExecuteDataAlign, $zvalAlign] = [
                $this->getSizeOfAlignedSize('zend_execute_data'),
                $this->getSizeOfAlignedSize('zval'),
            ];

            $callFrameSlot = (int)(($zendExecuteDataAlign + $zvalAlign - 1) / $zvalAlign);
        }

        return $callFrameSlot;
    }

    /**
     * @param string $type
     * @return int
     */
    private function getSizeOfAlignedSize(string $type): int
    {
        return $this->alignments[$type] ??= $this->zend->sizeof(
            $this->zend->type($type)
        );
    }

    /**
     * Macro implementation of zend_alloc:ZEND_MM_ALIGNED_SIZE
     *
     * <code>
     *  #define ZEND_MM_ALIGNED_SIZE(size) (((size) + ZEND_MM_ALIGNMENT - 1) & ZEND_MM_ALIGNMENT_MASK)
     * </code>
     *
     * @link https://github.com/php/php-src/blob/f0f5c415a6e0abc40514f97113deb52a343174ee/Zend/zend_alloc.h#L41
     * @param int $size
     * @return int
     */
    public function getAlignedSize(int $size): int
    {
        static $mmAlignedSize;

        return $mmAlignedSize ??= ($size + self::ZEND_MM_ALIGNMENT - 1) & self::ZEND_MM_ALIGNMENT_MASK;
    }

    /**
     * Macro implementation of zend_compile:ZEND_CALL_VAR_NUM
     *
     * <code>
     *  #define ZEND_CALL_VAR_NUM(call, n) (((zval*)(call)) + (ZEND_CALL_FRAME_SLOT + ((int)(n))))
     *  // vvv -- simplified -- vvv
     *  #define ZEND_CALL_VAR_NUM(call, n) (zval*)call + ZEND_CALL_FRAME_SLOT + (int)n
     * </code>
     *
     * @link https://github.com/php/php-src/blob/f0f5c415a6e0abc40514f97113deb52a343174ee/Zend/zend_compile.h#L570-L571
     * @param CData $call
     * @param int $n
     * @return CData
     */
    public function getCallVarNum(CData $call, int $n): CData
    {
        return $this->zend->cast('zval *', $call) + $this->getCallFrameSlot() + $n;
    }
}
