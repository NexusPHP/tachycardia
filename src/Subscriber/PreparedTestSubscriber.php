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

namespace Nexus\PHPUnit\Tachycardia\Subscriber;

use Nexus\PHPUnit\Tachycardia\SlowTest\SlowTestIdentifier;
use Nexus\PHPUnit\Tachycardia\Stopwatch;
use PHPUnit\Event\Code\TestMethod;
use PHPUnit\Event\Test\Prepared;
use PHPUnit\Event\Test\PreparedSubscriber;

/**
 * @internal
 */
final class PreparedTestSubscriber implements PreparedSubscriber
{
    public function __construct(
        private readonly Stopwatch $stopwatch,
    ) {}

    public function notify(Prepared $event): void
    {
        $test = $event->test();

        if (! $test instanceof TestMethod) {
            return; // @codeCoverageIgnore
        }

        $this->stopwatch->start(
            SlowTestIdentifier::from($test->id(), $test->file(), $test->line()),
            $event->telemetryInfo()->time(),
        );
    }
}
