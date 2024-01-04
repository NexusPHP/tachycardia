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

use Nexus\PHPUnit\Tachycardia\SlowTest\SlowTestIdentifier;
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

    public function start(SlowTestIdentifier $identifier, HRTime $startTime): void
    {
        $this->testTimes[$identifier->id()] = $startTime;
    }

    public function stop(SlowTestIdentifier $identifier, HRTime $stopTime): Duration
    {
        $testId = $identifier->id();

        if (\array_key_exists($testId, $this->testTimes)) {
            return $stopTime->duration($this->testTimes[$testId]);
        }

        return Duration::fromSecondsAndNanoseconds(0, 0);
    }
}
