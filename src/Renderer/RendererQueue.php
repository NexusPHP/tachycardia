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

namespace Nexus\PHPUnit\Tachycardia\Renderer;

use Nexus\PHPUnit\Tachycardia\SlowTest\SlowTestCollection;
use PHPUnit\Event\Telemetry\Info;

final class RendererQueue implements Renderer
{
    public function __construct(
        private readonly Renderer $configuredRender,
        private readonly CiRenderer $ciRenderer,
        private bool $monitor,
        private bool $monitorForGa,
    ) {}

    public function render(SlowTestCollection $collection, ?Info $telemetryInfo = null): string
    {
        $buffer = [];

        if ($this->monitor) {
            $buffer[] = $this->configuredRender->render($collection, $telemetryInfo);
        }

        if ($this->monitorForGa && $this->ciRenderer->runningInCi()) {
            $buffer[] = $this->ciRenderer->render($collection, $telemetryInfo);
        }

        return implode("\n", $buffer);
    }
}
