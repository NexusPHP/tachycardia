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

namespace Nexus\PHPUnit\Tachycardia\SlowTest;

use PHPUnit\Event\Code\Test;
use PHPUnit\Event\Telemetry\Duration;

/**
 * A representation of a slow test.
 *
 * @internal
 *
 * @immutable
 */
final class SlowTest
{
    public function __construct(
        private readonly SlowTestIdentifier $identifier,
        private readonly Duration $testTime,
        private readonly Duration $limit,
    ) {}

    public function identifier(): SlowTestIdentifier
    {
        return $this->identifier;
    }

    public function testTime(): Duration
    {
        return $this->testTime;
    }

    public function limit(): Duration
    {
        return $this->limit;
    }
}
