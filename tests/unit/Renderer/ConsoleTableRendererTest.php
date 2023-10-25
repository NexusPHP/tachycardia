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

namespace Nexus\PHPUnit\Tachycardia\Tests\Renderer;

use Nexus\PHPUnit\Tachycardia\Console\Color;
use Nexus\PHPUnit\Tachycardia\DurationFormatter;
use Nexus\PHPUnit\Tachycardia\Parameter\Precision;
use Nexus\PHPUnit\Tachycardia\Parameter\ReportCount;
use Nexus\PHPUnit\Tachycardia\Renderer\AbstractConsoleRenderer;
use Nexus\PHPUnit\Tachycardia\Renderer\ConsoleTableRenderer;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(AbstractConsoleRenderer::class)]
#[CoversClass(ConsoleTableRenderer::class)]
final class ConsoleTableRendererTest extends AbstractConsoleRendererTestCase
{
    public function testRendererWorksInNonEmptyCollection(): void
    {
        self::assertSame(
            <<<'TXT'


                Nexus\PHPUnit\Tachycardia\TachycardiaExtension identified this sole slow test:
                +-----------+---------------+-------------+
                | Test Case | Time Consumed | Time Limit  |
                +-----------+---------------+-------------+
                | Foo::bar  | 00:00:05.00   | 00:00:01.00 |
                +-----------+---------------+-------------+
                ...and 1 more test hidden from view.

                Slow tests: Time: 00:00:06.000 (75.00%)
                TXT,
            $this->renderer()->render($this->createSlowTestCollection(), $this->createTelemetryInfo()),
        );
    }

    protected function renderer(): ConsoleTableRenderer
    {
        $renderer = new ConsoleTableRenderer(Precision::fromInt(2));
        $renderer->setColor(new Color(false));
        $renderer->setDurationFormatter(new DurationFormatter());
        $renderer->setReportCount(ReportCount::from(1));

        return $renderer;
    }
}
