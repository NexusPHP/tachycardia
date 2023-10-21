<?php

declare(strict_types=1);

/**
 * This file is part of Nexus Tachycardia.
 *
 * (c) 2021 John Paul E. Balandan, CPA <paulbalandan@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Nexus\PHPUnit\Tachycardia\Tests\Metadata\Parser;

use Nexus\PHPUnit\Tachycardia\Attribute\TimeLimit;
use Nexus\PHPUnit\Tachycardia\Metadata\LimitCollection;
use Nexus\PHPUnit\Tachycardia\Metadata\LimitCollectionIterator;
use Nexus\PHPUnit\Tachycardia\Metadata\Parser\Registry;
use Nexus\PHPUnit\Tachycardia\Metadata\TimeLimitForClass;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[TimeLimit(1.0)]
#[CoversClass(Registry::class)]
#[CoversClass(LimitCollection::class)]
#[CoversClass(LimitCollectionIterator::class)]
final class RegistryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Registry::reset();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        Registry::reset();
    }

    public function testRegistryCanLookupExistingClassAnnotation(): void
    {
        $collection = Registry::parser()->forClass(self::class);

        self::assertFalse($collection->isEmpty());
        self::assertCount(1, $collection);
        self::assertContainsOnlyInstancesOf(TimeLimitForClass::class, $collection->asArray());

        self::assertSame(
            $collection,
            Registry::parser()->forClass(self::class),
            'Failed asserting that Registry returns cached collection instances.',
        );

        foreach ($collection as $key => $limit) {
            self::assertSame(0, $key);
            self::assertInstanceOf(TimeLimitForClass::class, $limit);
        }
    }
}
