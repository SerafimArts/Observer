<?php

/**
 * This file is part of Observer package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\Observer\Tests;

use Serafim\Observer\Observer;
use Serafim\Observer\Tests\Stub\WithoutProperty;
use Serafim\Observer\Tests\Stub\WithPrivateProperty;
use Serafim\Observer\Tests\Stub\WithProtectedProperty;
use Serafim\Observer\Tests\Stub\WithPublicProperty;

class ObserverTestCase extends TestCase
{
    /**
     * @return array|array[]
     */
    public function objectsDataProvider(): array
    {
        return [
            WithPublicProperty::class => [
                new WithPublicProperty(),
                'property',
                fn(): int => \random_int(\PHP_INT_MIN, \PHP_INT_MAX),
            ],
            WithoutProperty::class => [
                new WithoutProperty(),
                'property',
                fn(): int => \random_int(\PHP_INT_MIN, \PHP_INT_MAX),
            ],
            WithProtectedProperty::class => [
                new WithProtectedProperty(),
                'property',
                fn(): int => \random_int(\PHP_INT_MIN, \PHP_INT_MAX),
            ],
            WithPrivateProperty::class => [
                new WithPrivateProperty(),
                'property',
                fn(): int => \random_int(\PHP_INT_MIN, \PHP_INT_MAX),
            ],
        ];
    }

    /**
     * @dataProvider objectsDataProvider
     *
     * @param object $ctx
     * @param string $property
     * @param \Closure $data
     * @return void
     */
    public function testChanged(object $ctx, string $property, \Closure $data): void
    {
        $self = $this;
        $needle = $data();

        Observer::create($ctx)
            ->subscribe($property, function ($value, $before) use ($self, $needle) {
                $self->assertSame($needle, $value);
                $self->assertNull($before);
            });

        $ctx->$property = $needle;
    }
}
