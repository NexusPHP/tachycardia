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

namespace Nexus\PHPUnit\Tachycardia\Tests\Renderer;

use Nexus\PHPUnit\Tachycardia\Renderer\AbstractConsoleRenderer;
use Nexus\PHPUnit\Tachycardia\SlowTest\SlowTest;
use Nexus\PHPUnit\Tachycardia\SlowTest\SlowTestCollection;
use Nexus\PHPUnit\Tachycardia\SlowTest\SlowTestIdentifier;
use PHPUnit\Event\Telemetry\Duration;
use PHPUnit\Event\Telemetry\Info;
use PHPUnit\Event\Telemetry\Php81GarbageCollectorStatusProvider;
use PHPUnit\Event\Telemetry\System;
use PHPUnit\Event\Telemetry\SystemMemoryMeter;
use PHPUnit\Event\Telemetry\SystemStopWatch;
use PHPUnit\Framework\TestCase;

abstract class AbstractConsoleRendererTestCase extends TestCase
{
    final public function testRendererShowsEmptyStringIfCollectionIsEmpty(): void
    {
        $collection = $this->createSlowTestCollection();
        $collection->pop();
        $collection->pop();

        self::assertCount(0, $collection);
        self::assertSame('', $this->renderer()->render($collection, $this->createTelemetryInfo()));
    }

    abstract protected function renderer(): AbstractConsoleRenderer;

    protected function createSlowTestCollection(): SlowTestCollection
    {
        $collection = new SlowTestCollection();
        $collection->push($this->createMockSlowTest('Foo::bar', 5));
        $collection->push($this->createMockSlowTest('Foo::baz', 1));

        return $collection;
    }

    protected function createMockSlowTest(string $id, int $seconds): SlowTest
    {
        $identifier = SlowTestIdentifier::from($id, __FILE__);
        $testTime = Duration::fromSecondsAndNanoseconds($seconds, 0);
        $limit = Duration::fromSecondsAndNanoseconds(1, 0);

        return new SlowTest($identifier, $testTime, $limit);
    }

    protected function createTelemetryInfo(): Info
    {
        $snapshot = (new System(
            new SystemStopWatch(),
            new SystemMemoryMeter(),
            new Php81GarbageCollectorStatusProvider(),
        ))->snapshot();

        return new Info(
            $snapshot,
            Duration::fromSecondsAndNanoseconds(8, 0),
            $snapshot->memoryUsage(),
            Duration::fromSecondsAndNanoseconds(6, 0),
            $snapshot->memoryUsage()->diff($snapshot->memoryUsage()),
        );
    }
}
