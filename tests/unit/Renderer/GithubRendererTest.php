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

use Nexus\PHPUnit\Tachycardia\Parameter\Precision;
use Nexus\PHPUnit\Tachycardia\Renderer\GithubRenderer;
use Nexus\PHPUnit\Tachycardia\SlowTest\SlowTest;
use Nexus\PHPUnit\Tachycardia\SlowTest\SlowTestCollection;
use PHPUnit\Event\Code\Test;
use PHPUnit\Event\Telemetry\Duration;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(GithubRenderer::class)]
final class GithubRendererTest extends TestCase
{
    public function testRunningInCiMatchesCiCondition(): void
    {
        $renderer = new GithubRenderer(Precision::fromInt(4));

        self::assertSame(getenv('GITHUB_ACTIONS') !== false, $renderer->runningInCi());
    }

    public function testRendererWorksProperly(): void
    {
        $renderer = new GithubRenderer(Precision::fromInt(4));
        $id = uniqid();

        self::assertSame(
            sprintf(
                "::warning file=%s,line=1,col=0::Took 2.0000s from 1.0000s limit to run %s\n",
                str_replace((string) getcwd(), '', __FILE__),
                $id,
            ),
            $renderer->render($this->createSlowTestCollection($id)),
        );
    }

    private function createSlowTestCollection(string $id): SlowTestCollection
    {
        $collection = new SlowTestCollection();
        $collection->push($this->createMockSlowTest($id));

        return $collection;
    }

    private function createMockSlowTest(string $id): SlowTest
    {
        /** @var Stub&Test $test */
        $test = self::createStub(Test::class);
        $test->method('id')->willReturn($id);
        $test->method('file')->willReturn(__FILE__);

        $testTime = Duration::fromSecondsAndNanoseconds(2, 500);
        $limit = Duration::fromSecondsAndNanoseconds(1, 0);

        return new SlowTest($test, $testTime, $limit);
    }
}
