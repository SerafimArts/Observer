<?php

/**
 * This file is part of Observer package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\Observer;

use FFI\CData;
use FFI\CType;
use Serafim\FFILoader\LibraryInformation;
use Serafim\Observer\Library\Loader;
use Serafim\Observer\Zend\ExecutorGlobals;
use Serafim\Observer\Zend\Macros;

/**
 * @property-read \FFI $ffi
 *
 * @property-read ExecutorGlobals $executor
 * @property-read Macros $macros
 */
final class Zend
{
    /**
     * @var \FFI
     */
    public \FFI $ffi;

    /**
     * @var ExecutorGlobals
     */
    public ExecutorGlobals $executor;

    /**
     * @var Macros
     */
    public Macros $macros;

    /**
     * Zend constructor.
     *
     * @param \FFI $ffi
     *
     * @noinspection PhpUndefinedFieldInspection
     */
    public function __construct(\FFI $ffi)
    {
        $this->ffi = $ffi;

        $this->macros = new Macros($this);
        $this->executor = new ExecutorGlobals($this, $this->ffi->executor_globals);
    }

    /**
     * @return static
     */
    public static function load(): self
    {
        return self::fromLibrary(Loader::load());
    }

    /**
     * @param LibraryInformation $lib
     * @return static
     */
    public static function fromLibrary(LibraryInformation $lib): self
    {
        return new self($lib->ffi);
    }

    /**
     * @param CData|CType $data
     * @return int
     */
    public function sizeof($data): int
    {
        return $this->ffi::sizeof($data);
    }

    /**
     * @param string $type
     * @return CType
     * @noinspection StaticInvocationViaThisInspection
     */
    public function type(string $type): CType
    {
        return $this->ffi->type($type);
    }

    /**
     * @param string $type
     * @param bool $owned
     * @param bool $persistent
     * @return CData
     *
     * @noinspection StaticInvocationViaThisInspection
     */
    public function new(string $type, bool $owned = true, bool $persistent = false): CData
    {
        return $this->ffi->new($type, $owned, $persistent);
    }

    /**
     * @param CData $target
     * @param mixed $source
     * @param int $size
     * @return void
     */
    public function memcpy(CData $target, &$source, int $size): void
    {
        $this->ffi::memcpy($target, $source, $size);
    }

    /**
     * @param string $type
     * @param CData $data
     * @return CData
     * @noinspection StaticInvocationViaThisInspection
     */
    public function cast(string $type, CData $data): CData
    {
        return $this->ffi->cast($type, $data);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->ffi->$name;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments = [])
    {
        return $this->ffi->$name(...$arguments);
    }
}
