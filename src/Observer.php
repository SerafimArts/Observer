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
use Serafim\Observer\Zend\Value\ObjectValue;

final class Observer implements ObserverInterface, FactoryInterface
{
    /**
     * @var string
     */
    private const ERROR_PROPERTY_NOT_FOUND = 'Property %s::$%s not defined';

    /**
     * @var Zend
     */
    private Zend $zend;

    /**
     * @var object
     */
    private object $object;

    /**
     * @var CData
     */
    private CData $zendObject;

    /**
     * @var CData|null
     */
    private ?CData $handler;

    /**
     * @var array|Observable[]
     */
    private array $properties = [];

    /**
     * ObjectStorage constructor.
     *
     * @param Zend $zend
     * @param object $object
     */
    public function __construct(Zend $zend, object $object)
    {
        $this->zend = $zend;
        $this->object = $object;
        $this->zendObject = ObjectValue::create($zend, $object);
        $this->handler = $this->bootHandlers();
    }

    /**
     * @return CData
     */
    private function bootHandlers(): CData
    {
        $original = $this->zendObject->handlers[0]->write_property;

        $handlers = $this->restoreObjectHandlers();

        $handlers->write_property = \Closure::fromCallable([$this, 'onWrite']);

        return $original;
    }

    /**
     * @return CData|object
     */
    private function restoreObjectHandlers(): CData
    {
        /** @var object|CData $handlers */
        $handlers = $this->zend->new('zend_object_handlers', false, true);
        $standard = $this->zend->std_object_handlers;

        $this->zend->memcpy($handlers, $standard, $this->zend->sizeof($standard));

        $this->zendObject->handlers = $handlers;

        return $handlers;
    }

    /**
     * @param object $object
     * @return ObserverInterface|Observer|object
     */
    public static function create(object $object): ObserverInterface
    {
        static $zend;

        return new self($zend ??= Zend::load(), $object);
    }

    /**
     * @param string $name
     * @return ObservableInterface
     */
    public function __get(string $name): ObservableInterface
    {
        $this->assertExists($name);

        return $this->getProperty($name);
    }

    /**
     * @param string $property
     * @return void
     */
    private function assertExists(string $property): void
    {
        if (! \property_exists($this->object, $property)) {
            $error = \sprintf(self::ERROR_PROPERTY_NOT_FOUND, \get_class($this->object), $property);

            throw new \InvalidArgumentException($error);
        }
    }

    /**
     * @param string $property
     * @return ObservableInterface
     */
    private function getProperty(string $property): ObservableInterface
    {
        return $this->properties[$property] ??= new Observable($this->object, $property);
    }

    /**
     * @param string $property
     * @param \Closure $then
     * @return SubscriptionInterface
     */
    public function subscribe(string $property, \Closure $then): SubscriptionInterface
    {
        return $this->getProperty($property)
            ->subscribe($then)
        ;
    }

    /**
     * typedef zval *(*zend_object_write_property_t)(zval *object, zval *member, zval *value, void **cache_slot);
     *
     * @param CData $object
     * @param CData $member
     * @param CData $value
     * @param CData $cache
     * @return mixed
     * @noinspection PhpUndefinedFieldInspection
     * @throws \Throwable
     */
    private function onWrite($object, $member, $value, $cache)
    {
        $name = \FFI::string($member->value->str->val);

        return $this->wrap($name, function () use ($object, $member, $value, $cache) {
            return $this->handle([$object, $member, $value, $cache]);
        });
    }

    /**
     * @param string $property
     * @param \Closure $then
     * @return mixed
     */
    private function wrap(string $property, \Closure $then)
    {
        if (isset($this->properties[$property])) {
            $observable = $this->properties[$property];

            $before = $observable->getValue();
            $result = $then();
            $after = $observable->getValue();

            if ($before !== $after) {
                $observable->notify($observable->getValue(), $before);
            }

            return $result;
        }

        return $then();
    }

    /**
     * @param array $args
     * @return mixed
     */
    private function handle(array $args)
    {
        /** @var callable $handler */
        $handler = $this->handler;

        return $this->zend->executor->scoped(
            $this->zendObject->ce,
            fn () => $handler(...$args)
        );
    }
}
