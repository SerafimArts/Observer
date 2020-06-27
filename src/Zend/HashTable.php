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
use Serafim\Observer\Zend\Value\StringValue;

/**
 * <code>
 *  struct _zend_array {
 *      zend_refcounted_h gc;
 *      union {
 *          struct {
 *              zend_uchar    flags;
 *              zend_uchar    _unused;
 *              zend_uchar    nIteratorsCount;
 *              zend_uchar    _unused2;
 *          } v;
 *          uint32_t flags;
 *      } u;
 *      uint32_t          nTableMask;
 *      Bucket           *arData;
 *      uint32_t          nNumUsed;
 *      uint32_t          nNumOfElements;
 *      uint32_t          nTableSize;
 *      uint32_t          nInternalPointer;
 *      zend_long         nNextFreeElement;
 *      dtor_func_t       pDestructor;
 *  };
 * </code>
 */
class HashTable implements \Countable, \IteratorAggregate, \ArrayAccess
{
    /**
     * @var CData
     */
    private CData $ptr;

    /**
     * @var Zend
     */
    private Zend $zend;

    /**
     * HashTable constructor.
     *
     * @param Zend $zend
     * @param CData $ptr
     */
    public function __construct(Zend $zend, CData $ptr)
    {
        $this->ptr = $ptr;
        $this->zend = $zend;
    }

    /**
     * @param string $key
     * @return CData|null
     */
    public function findByKey(string $key): ?CData
    {
        $lower = \strtolower($key);

        foreach ($this->getIterator() as $name => $value) {
            if (\FFI::string($name->val) === $lower) {
                return $value;
            }
        }

        return null;
    }

    /**
     * @param int $id
     * @return CData|null
     */
    public function findByIndex(int $id): ?CData
    {
        if ($this->offsetExists($id)) {
            return $this->getByIndex($id);
        }

        return null;
    }

    /**
     * @param int $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        \assert(\is_int($offset));

        return $offset < $this->count();
    }

    /**
     * @param int $offset
     * @return CData|null
     */
    public function offsetGet($offset): ?CData
    {
        \assert(\is_int($offset));

        return $this->findByIndex($offset);
    }

    /**
     * @param int $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        \assert(\is_int($offset));

        throw new \LogicException('HashTable is immutable');
    }

    /**
     * @param int $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
        \assert(\is_int($offset));

        throw new \LogicException('HashTable is immutable');
    }

    /**
     * @return \Traversable
     *
     * @noinspection PhpUndefinedFieldInspection
     */
    public function getIterator(): \Traversable
    {
        [$index, $length] = [0, $this->count()];

        while ($index < $length) {
            $item = $this->getByIndex($index++);

            if (Type::is($item->val, Type::IS_UNDEF)) {
                continue;
            }

            yield $item->key => $item->val;
        }
    }

    /**
     * @param int $index
     * @return CData Bucket
     *
     * @noinspection PhpUndefinedFieldInspection
     */
    private function getByIndex(int $index): CData
    {
        return $this->ptr->arData[$index];
    }

    /**
     * @return int
     *
     * @noinspection PhpUndefinedFieldInspection
     */
    public function count(): int
    {
        return $this->ptr->nNumOfElements;
    }
}
