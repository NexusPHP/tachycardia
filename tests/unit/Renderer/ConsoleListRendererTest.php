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
use Nexus\PHPUnit\Tachycardia\Renderer\ConsoleListRenderer;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(AbstractConsoleRenderer::class)]
#[CoversClass(ConsoleListRenderer::class)]
final class ConsoleListRendererTest extends AbstractConsoleRendererTestCase
{
    public function testRendererWorksInNonEmptyCollection(): void
    {
        self::assertSame(
            <<<TXT


                Nexus\\PHPUnit\\Tachycardia\\TachycardiaExtension identified this sole slow test:
                \xE2\x9A\xA0  Took 5.00s from 1.00s limit to run Foo::bar
                ...and 1 more test hidden from view.

                TXT,
            $this->renderer()->render($this->createSlowTestCollection()),
        );
    }

    protected function renderer(): ConsoleListRenderer
    {
        $renderer = new ConsoleListRenderer(Precision::fromInt(2));
        $renderer->setColor(new Color(false));
        $renderer->setDurationFormatter(new DurationFormatter());
        $renderer->setReportCount(ReportCount::from(1));

        return $renderer;
    }
}
