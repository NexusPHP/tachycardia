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

namespace Nexus\PHPUnit\Tachycardia\Metadata;

use Nexus\PHPUnit\Tachycardia\Parameter\Limit as LimitParameter;
use PHPUnit\Event\Telemetry\Duration;

/**
 * @internal
 */
final class TimeLimitForMethod implements Limit
{
    public function __construct(
        private readonly float $seconds,
    ) {}

    public function hasTimeLimit(): bool
    {
        return true;
    }

    public function getTimeLimit(): Duration
    {
        return LimitParameter::fromSeconds($this->seconds)->duration();
    }

    public function isMoreImportantThan(Limit $other): bool
    {
        if (! $other->hasTimeLimit()) {
            return false;
        }

        if ($other instanceof TimeLimitForClass) {
            return true;
        }

        return $this->getTimeLimit()->isLessThan($other->getTimeLimit());
    }
}
