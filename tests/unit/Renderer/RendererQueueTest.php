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

use Nexus\PHPUnit\Tachycardia\Renderer\CiRenderer;
use Nexus\PHPUnit\Tachycardia\Renderer\Renderer;
use Nexus\PHPUnit\Tachycardia\Renderer\RendererQueue;
use Nexus\PHPUnit\Tachycardia\SlowTest\SlowTest;
use Nexus\PHPUnit\Tachycardia\SlowTest\SlowTestCollection;
use Nexus\PHPUnit\Tachycardia\SlowTest\SlowTestIdentifier;
use PHPUnit\Event\Telemetry\Duration;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(RendererQueue::class)]
final class RendererQueueTest extends TestCase
{
    private const CONFIGURED_RENDERER_OUTPUT = "<configured-renderer-output>\n";
    private const CI_RENDERER_OUTPUT = "<ci-renderer-output>\n";
    private const COMBINED_RENDERER_OUTPUT = self::CONFIGURED_RENDERER_OUTPUT."\n".self::CI_RENDERER_OUTPUT;

    /**
     * @return iterable<string, array{0: bool, 1: bool, 2: bool, 3: string}>
     */
    public static function provideQueueInDifferentSituationCases(): iterable
    {
        yield 'not monitor and ci' => [false, false, false, ''];

        yield 'not monitor for local' => [true, false, true, self::CI_RENDERER_OUTPUT];

        yield 'not running in ci' => [false, true, true, self::CONFIGURED_RENDERER_OUTPUT];

        yield 'not monitor for ci' => [true, true, false, self::CONFIGURED_RENDERER_OUTPUT];

        yield 'normal env' => [true, true, true, self::COMBINED_RENDERER_OUTPUT];
    }

    #[DataProvider('provideQueueInDifferentSituationCases')]
    public function testQueueInDifferentSituations(bool $runningInCi, bool $monitor, bool $monitorForGa, string $expected): void
    {
        $queue = new RendererQueue($this->createConfiguredRenderer(), $this->createCiRenderer($runningInCi), $monitor, $monitorForGa);

        self::assertSame($expected, $queue->render($this->createSlowTestCollection()));
    }

    private function createConfiguredRenderer(): Renderer&Stub
    {
        /** @var Renderer&Stub $configuredRenderer */
        $configuredRenderer = self::createStub(Renderer::class);
        $configuredRenderer->method('render')->willReturn(self::CONFIGURED_RENDERER_OUTPUT);

        return $configuredRenderer;
    }

    private function createCiRenderer(bool $runningInCi): CiRenderer&Stub
    {
        /** @var CiRenderer&Stub $ciRenderer */
        $ciRenderer = self::createStub(CiRenderer::class);
        $ciRenderer->method('render')->willReturn(self::CI_RENDERER_OUTPUT);
        $ciRenderer->method('runningInCi')->willReturn($runningInCi);

        return $ciRenderer;
    }

    private function createSlowTestCollection(): SlowTestCollection
    {
        $collection = new SlowTestCollection();
        $collection->push($this->createMockSlowTest());
        $collection->push($this->createMockSlowTest());
        $collection->push($this->createMockSlowTest());

        return $collection;
    }

    private function createMockSlowTest(): SlowTest
    {
        $identifier = SlowTestIdentifier::from('Foo::bar', __FILE__);
        $testTime = Duration::fromSecondsAndNanoseconds(mt_rand(1, 10), mt_rand(500, 1_000));
        $limit = Duration::fromSecondsAndNanoseconds(1, 0);

        return new SlowTest($identifier, $testTime, $limit);
    }
}
