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

namespace Nexus\PHPUnit\Tachycardia\Subscriber\Test;

use Nexus\PHPUnit\Tachycardia\SlowTest\SlowTestIdentifier;
use Nexus\PHPUnit\Tachycardia\Stopwatch;
use PHPUnit\Event;

/**
 * @internal
 */
final class PreparedSubscriber implements Event\Test\PreparedSubscriber
{
    public function __construct(
        private readonly Stopwatch $stopwatch,
    ) {}

    public function notify(Event\Test\Prepared $event): void
    {
        $this->stopwatch->start(
            SlowTestIdentifier::fromTest($event->test()),
            $event->telemetryInfo()->time(),
        );
    }
}
