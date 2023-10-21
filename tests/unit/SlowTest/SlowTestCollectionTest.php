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

namespace Nexus\PHPUnit\Tachycardia\Tests\SlowTest;

use Nexus\PHPUnit\Tachycardia\SlowTest\SlowTest;
use Nexus\PHPUnit\Tachycardia\SlowTest\SlowTestCollection;
use Nexus\PHPUnit\Tachycardia\SlowTest\SlowTestCollectionIterator;
use PHPUnit\Event\Code\Test;
use PHPUnit\Event\Telemetry\Duration;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(SlowTestCollection::class)]
#[CoversClass(SlowTestCollectionIterator::class)]
final class SlowTestCollectionTest extends TestCase
{
    public function testCollectionCanBeEmpty(): void
    {
        $collection = new SlowTestCollection();

        self::assertTrue($collection->isEmpty());
    }

    public function testCollectionIsNotEmptyWhenPushed(): void
    {
        $collection = new SlowTestCollection();
        $slowTest = $this->createMockSlowTest();

        $collection->push($slowTest);
        self::assertFalse($collection->isEmpty());
    }

    public function testCollectionReturnsSlowTestOnLifo(): void
    {
        $collection = new SlowTestCollection();

        $slowTest1 = $this->createMockSlowTest();
        $collection->push($slowTest1);
        self::assertCount(1, $collection);

        $slowTest2 = $this->createMockSlowTest();
        $collection->push($slowTest2);
        self::assertCount(2, $collection);

        self::assertSame($slowTest2, $collection->pop());
        self::assertFalse($collection->isEmpty());
        self::assertCount(1, $collection);

        self::assertSame($slowTest1, $collection->pop());
        self::assertTrue($collection->isEmpty());
        self::assertCount(0, $collection);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Collection is empty.');
        $collection->pop();
    }

    public function testCollectionIgnoresSameSlowTestOfLesserTime(): void
    {
        /** @var MockObject&Test $test */
        $test = $this->createMock(Test::class);
        $test->method('id')->willReturn('foo');

        $collection = new SlowTestCollection();
        $slowTest1 = new SlowTest($test, Duration::fromSecondsAndNanoseconds(2, 0), Duration::fromSecondsAndNanoseconds(1, 0));
        $slowTest2 = new SlowTest($test, Duration::fromSecondsAndNanoseconds(1, 500), Duration::fromSecondsAndNanoseconds(1, 0));

        $collection->push($slowTest1);
        self::assertCount(1, $collection);

        $collection->push($slowTest2);
        self::assertCount(1, $collection);
        self::assertSame($slowTest1, $collection->pop());
    }

    public function testCollectionAsArratSortsSlowTestsFromHighestToLowest(): void
    {
        $collection = new SlowTestCollection();
        $slowTest1 = $this->createMockSlowTest();
        $slowTest2 = $this->createMockSlowTest();
        $slowTest3 = $this->createMockSlowTest();
        $slowTest4 = $this->createMockSlowTest();

        $collection->push($slowTest1);
        $collection->push($slowTest2);
        $collection->push($slowTest3);
        $collection->push($slowTest4);

        $slowTests = $collection->asArray();

        $ordered1 = $slowTests[0];
        $ordered2 = $slowTests[1];
        $ordered3 = $slowTests[2];
        $ordered4 = $slowTests[3];
        self::assertTrue($ordered1->testTime()->isGreaterThan($ordered2->testTime()));
        self::assertTrue($ordered2->testTime()->isGreaterThan($ordered3->testTime()));
        self::assertTrue($ordered3->testTime()->isGreaterThan($ordered4->testTime()));
        self::assertFalse($ordered4->testTime()->isGreaterThan($ordered1->testTime()));
    }

    public function testIteratingOfCollection(): void
    {
        $collection = new SlowTestCollection();
        $slowTest1 = $this->createMockSlowTest();

        $collection->push($slowTest1);

        foreach ($collection as $index => $slowTest) {
            self::assertIsInt($index);
            self::assertSame($slowTest1, $slowTest);
        }
    }

    private function createMockSlowTest(): SlowTest
    {
        /** @var MockObject&Test $test */
        $test = $this->createMock(Test::class);
        $test->method('id')->willReturn(uniqid());

        $testTime = Duration::fromSecondsAndNanoseconds(mt_rand(1, 10), mt_rand(500, 1_000));
        $limit = Duration::fromSecondsAndNanoseconds(1, 0);

        return new SlowTest($test, $testTime, $limit);
    }
}
