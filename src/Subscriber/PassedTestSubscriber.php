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
use Nexus\PHPUnit\Tachycardia\SlowTest\SlowTestIdentifier;
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
        \assert($test instanceof TestMethod);

        $limit = Registry::parser()->forClassAndMethod(
            $test->className(),
            $test->methodName(),
        )->reduce($this->defaultTimeLimit);

        if (! $limit->hasTimeLimit()) {
            return;
        }

        $identifier = SlowTestIdentifier::from($test->id(), $test->file(), $test->line());
        $duration = $this->stopwatch->stop($identifier, $event->telemetryInfo()->time());

        if ($duration->isLessThan($limit->getTimeLimit())) {
            return;
        }

        $this->collection->push(new SlowTest(
            $identifier,
            $duration,
            $limit->getTimeLimit(),
        ));
    }
}
