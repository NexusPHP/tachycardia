<?php

declare(strict_types=1);

/**
 * This file is part of NexusPHP Tachycardia.
 *
 * (c) 2021 John Paul E. Balandan, CPA <paulbalandan@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Nexus\PHPUnit\Extension\Tests;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class SlowTestsTest extends TestCase
{
    public function testFastTest(): void
    {
        self::assertTrue(true);
    }

    public function testSlowTest(): void
    {
        sleep(2);
        self::assertTrue(true);
    }

    public function testSlowerTest(): void
    {
        sleep(3);
        self::assertTrue(true);
    }

    public function testSlowestTest(): void
    {
        sleep(4);
        self::assertTrue(true);
    }

    /**
     * @dataProvider provideTime
     *
     * @param int $time
     */
    public function testWithProvider(int $time): void
    {
        sleep($time);
        self::assertTrue(true);
    }

    /** @return int[][] */
    public function provideTime(): iterable
    {
        return [
            'slow'    => [5],
            'slower'  => [6],
            'slowest' => [7],
        ];
    }

    /**
     * This should not be reported as slow.
     *
     * @timeLimit 3
     */
    public function testCustomHigherLimit(): void
    {
        sleep(2);
        self::assertTrue(true);
    }

    /**
     * This should be reported as slow.
     *
     * @timeLimit 0.5
     */
    public function testCustomLowerLimit(): void
    {
        sleep(1);
        self::assertTrue(true);
    }
}