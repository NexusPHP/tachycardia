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

use Nexus\PHPUnit\Tachycardia\Renderer\AbstractConsoleRenderer;
use Nexus\PHPUnit\Tachycardia\SlowTest\SlowTest;
use Nexus\PHPUnit\Tachycardia\SlowTest\SlowTestCollection;
use PHPUnit\Event\Code\Test;
use PHPUnit\Event\Telemetry\Duration;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

abstract class AbstractConsoleRendererTestCase extends TestCase
{
    final public function testRendererShowsEmptyStringIfCollectionIsEmpty(): void
    {
        $collection = $this->createSlowTestCollection();
        $collection->pop();
        $collection->pop();

        self::assertCount(0, $collection);
        self::assertSame('', $this->renderer()->render($collection));
    }

    abstract protected function renderer(): AbstractConsoleRenderer;

    protected function createSlowTestCollection(): SlowTestCollection
    {
        $collection = new SlowTestCollection();
        $collection->push($this->createMockSlowTest('Foo::bar', 5));
        $collection->push($this->createMockSlowTest('Foo::baz', 1));

        return $collection;
    }

    protected function createMockSlowTest(string $id, int $seconds): SlowTest
    {
        /** @var Stub&Test $test */
        $test = self::createStub(Test::class);
        $test->method('id')->willReturn($id);

        $testTime = Duration::fromSecondsAndNanoseconds($seconds, 0);
        $limit = Duration::fromSecondsAndNanoseconds(1, 0);

        return new SlowTest($test, $testTime, $limit);
    }
}
