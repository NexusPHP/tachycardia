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
use Nexus\PHPUnit\Tachycardia\Renderer\ConsoleListRenderer;
use Nexus\PHPUnit\Tachycardia\Renderer\GithubRenderer;
use Nexus\PHPUnit\Tachycardia\Renderer\RendererFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(RendererFactory::class)]
final class RendererFactoryTest extends TestCase
{
    public function testFactoryThrowsExceptionOnInvalidRenderer(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid format "listed" given. Expected one of "github", "list", and "table".');

        RendererFactory::from('listed', Precision::fromInt(2), ReportCount::from(10), new Color(true), new DurationFormatter());
    }

    public function testFactoryReturnsInstanceInSupportedRenderersMap(): void
    {
        $list = RendererFactory::from('list', Precision::fromInt(2), ReportCount::from(10), new Color(true), new DurationFormatter());
        $github = RendererFactory::from('github', Precision::fromInt(2), ReportCount::from(10), new Color(true), new DurationFormatter());

        self::assertInstanceOf(ConsoleListRenderer::class, $list);
        self::assertInstanceOf(GithubRenderer::class, $github);
    }
}
