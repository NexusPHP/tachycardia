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

namespace Nexus\PHPUnit\Tachycardia\Attribute;

/**
 * @immutable
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
final class TimeLimit
{
    public function __construct(
        private readonly float $seconds,
    ) {}

    public function seconds(): float
    {
        return $this->seconds;
    }
}
