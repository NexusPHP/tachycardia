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

namespace Nexus\PHPUnit\Tachycardia;

use PHPUnit\Event\Code\Test;
use PHPUnit\Event\Telemetry\Duration;
use PHPUnit\Event\Telemetry\HRTime;

/**
 * @internal
 */
final class Stopwatch
{
    /**
     * @var array<string, HRTime>
     */
    private array $testTimes = [];

    public function start(Test $test, HRTime $startTime): void
    {
        $this->testTimes[$test->id()] = $startTime;
    }

    public function stop(Test $test, HRTime $stopTime): Duration
    {
        $testId = $test->id();

        if (\array_key_exists($testId, $this->testTimes)) {
            return $stopTime->duration($this->testTimes[$testId]);
        }

        return Duration::fromSecondsAndNanoseconds(0, 0);
    }
}
