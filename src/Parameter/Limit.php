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

namespace Nexus\PHPUnit\Tachycardia\Parameter;

use PHPUnit\Event\Telemetry\Duration;

/**
 * @internal
 *
 * @immutable
 */
final class Limit
{
    private function __construct(
        private readonly Duration $duration,
    ) {}

    /**
     * @throws \InvalidArgumentException when $seconds is <= 0
     */
    public static function fromSeconds(float $seconds): self
    {
        if ($seconds <= 0.0) {
            throw new \InvalidArgumentException('Seconds cannot be less than or equal to zero.');
        }

        $wholeSeconds = (int) floor($seconds);
        $nanoseconds = (int) (($seconds - $wholeSeconds) * 1_000_000_000);

        return new self(Duration::fromSecondsAndNanoseconds($wholeSeconds, $nanoseconds));
    }

    public function duration(): Duration
    {
        return $this->duration;
    }
}
