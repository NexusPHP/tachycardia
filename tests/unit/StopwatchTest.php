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

namespace Nexus\PHPUnit\Tachycardia\Tests;

use Nexus\PHPUnit\Tachycardia\Stopwatch;
use PHPUnit\Event\Code\Test;
use PHPUnit\Event\Telemetry\HRTime;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(Stopwatch::class)]
final class StopwatchTest extends TestCase
{
    public function testStopwatch(): void
    {
        $start = HRTime::fromSecondsAndNanoseconds(1, 99_805_612);
        $end = HRTime::fromSecondsAndNanoseconds(2, 100_000_000);

        /** @var MockObject&Test $test */
        $test = $this->createMock(Test::class);
        $test->method('id')->willReturn('Test::bar');

        /** @var MockObject&Test $otherTest */
        $otherTest = $this->createMock(Test::class);
        $otherTest->method('id')->willReturn('Test::baz');

        $stopwatch = new Stopwatch();
        $stopwatch->start($test, $start);

        self::assertSame(
            $end->duration($start)->asFloat(),
            $stopwatch->stop($test, $end)->asFloat(),
        );
        self::assertSame(0.0, $stopwatch->stop($otherTest, $end)->asFloat());
    }
}
