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

/**
 * <code>
 *  struct _zend_execute_data {
 *      const zend_op       *opline;
 *      zend_execute_data   *call;
 *      zval                *return_value;
 *      zend_function       *func;
 *      zval                 This;
 *      zend_execute_data   *prev_execute_data;
 *      zend_array          *symbol_table;
 *      void               **run_time_cache;
 *  };
 * <code>
 */
class ExecuteData
{
    /**
     * @var int
     */
    private const ERROR_INVALID_ARGUMENT = 'Argument #%d is requested, however only %d is available';

    /**
     * @var Zend
     */
    private Zend $zend;

    /**
     * @var CData
     */
    public CData $ptr;

    /**
     * ExecuteData constructor.
     *
     * @param Zend $zend
     * @param CData $ptr
     */
    public function __construct(Zend $zend, CData $ptr)
    {
        $this->zend = $zend;
        $this->ptr = $ptr;
    }

    /**
     * @param int $index
     * @return CData Returns zval_struct pointer
     *
     * @noinspection PhpUndefinedFieldInspection
     */
    public function getArgument(int $index): CData
    {
        if ($index >= $available = $this->ptr->This->u2->num_args) {
            throw new \OutOfBoundsException(\sprintf(self::ERROR_INVALID_ARGUMENT, $index + 1, $available));
        }

        return $this->zend->macros->getCallVarNum($this->ptr, $index);
    }

    /**
     * @return ExecuteData|null
     * @noinspection PhpUndefinedFieldInspection
     */
    public function getPrevious(): ?ExecuteData
    {
        if ($this->ptr->prev_execute_data !== null) {
            return new ExecuteData($this->zend, $this->ptr->prev_execute_data);
        }

        return null;
    }
}
