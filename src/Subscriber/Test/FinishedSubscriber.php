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

use Nexus\PHPUnit\Tachycardia\Metadata\Limit;
use Nexus\PHPUnit\Tachycardia\Metadata\NoTimeLimitForMethod;
use Nexus\PHPUnit\Tachycardia\Metadata\Parser\Registry;
use Nexus\PHPUnit\Tachycardia\Metadata\TimeLimitForMethod;
use Nexus\PHPUnit\Tachycardia\Parameter\Limit as LimitParameter;
use Nexus\PHPUnit\Tachycardia\SlowTest\SlowTest;
use Nexus\PHPUnit\Tachycardia\SlowTest\SlowTestCollection;
use Nexus\PHPUnit\Tachycardia\SlowTest\SlowTestIdentifier;
use Nexus\PHPUnit\Tachycardia\Stopwatch;
use PHPUnit\Event;

final class FinishedSubscriber implements Event\Test\FinishedSubscriber
{
    public function __construct(
        private readonly SlowTestCollection $collection,
        private readonly Stopwatch $stopwatch,
        private readonly LimitParameter $defaultTimeLimit,
    ) {}

    public function notify(Event\Test\Finished $event): void
    {
        $test = $event->test();
        $limit = $this->determineTimeLimit($test);

        if (! $limit->hasTimeLimit()) {
            return;
        }

        $identifier = SlowTestIdentifier::fromTest($test);
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

    private function determineTimeLimit(Event\Code\Test $test): Limit
    {
        if ($test instanceof Event\Code\TestMethod) {
            return Registry::parser()->forClassAndMethod(
                $test->className(),
                $test->methodName(),
            )->reduce($this->defaultTimeLimit);
        }

        // @codeCoverageIgnoreStart
        if ($test instanceof Event\Code\Phpt) {
            return new TimeLimitForMethod($this->defaultTimeLimit->duration()->asFloat());
        }

        return new NoTimeLimitForMethod();
        // @codeCoverageIgnoreEnd
    }
}
