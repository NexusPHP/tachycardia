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

use PHPUnit\Event\Telemetry\Duration;

/**
 * @internal
 */
final class NoTimeLimitForMethod implements Limit
{
    public function hasTimeLimit(): bool
    {
        return false;
    }

    public function getTimeLimit(): Duration
    {
        return Duration::fromSecondsAndNanoseconds(0, 0);
    }

    public function isMoreImportantThan(Limit $other): bool
    {
        if ($other->hasTimeLimit()) {
            return true;
        }

        return $other instanceof NoTimeLimitForClass;
    }
}
