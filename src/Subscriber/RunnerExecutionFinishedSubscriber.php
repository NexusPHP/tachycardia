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

use Nexus\PHPUnit\Tachycardia\Renderer\Renderer;
use Nexus\PHPUnit\Tachycardia\SlowTest\SlowTestCollection;
use PHPUnit\Event\TestRunner\ExecutionFinished;
use PHPUnit\Event\TestRunner\ExecutionFinishedSubscriber;

/**
 * @internal
 */
final class RunnerExecutionFinishedSubscriber implements ExecutionFinishedSubscriber
{
    public function __construct(
        private readonly SlowTestCollection $collection,
        private readonly Renderer $renderer,
    ) {}

    public function notify(ExecutionFinished $event): void
    {
        $render = $this->renderer->render($this->collection, $event->telemetryInfo());

        echo $render;
    }
}
