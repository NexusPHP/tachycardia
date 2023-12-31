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

use Nexus\PHPUnit\Tachycardia\Metadata\Parser\Registry;
use Nexus\PHPUnit\Tachycardia\Parameter\Limit as ParameterLimit;
use Nexus\PHPUnit\Tachycardia\SlowTest\SlowTest;
use Nexus\PHPUnit\Tachycardia\SlowTest\SlowTestCollection;
use Nexus\PHPUnit\Tachycardia\Stopwatch;
use PHPUnit\Event\Code\TestMethod;
use PHPUnit\Event\Test\Passed;
use PHPUnit\Event\Test\PassedSubscriber;

final class PassedTestSubscriber implements PassedSubscriber
{
    public function __construct(
        private readonly SlowTestCollection $collection,
        private readonly Stopwatch $stopwatch,
        private readonly ParameterLimit $defaultTimeLimit,
    ) {}

    public function notify(Passed $event): void
    {
        $test = $event->test();

        if (! $test instanceof TestMethod) {
            return; // @codeCoverageIgnore
        }

        $limit = Registry::parser()->forClassAndMethod(
            $test->className(),
            $test->methodName(),
        )->reduce($this->defaultTimeLimit);

        if (! $limit->hasTimeLimit()) {
            return;
        }

        $duration = $this->stopwatch->stop($test, $event->telemetryInfo()->time());

        if ($duration->isLessThan($limit->getTimeLimit())) {
            return;
        }

        $this->collection->push(new SlowTest(
            test: $test,
            testTime: $duration,
            limit: $limit->getTimeLimit(),
        ));
    }
}
