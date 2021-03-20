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

use Nexus\PHPUnit\Extension\Tachycardia;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class TachycardiaTest extends TestCase
{
    public function testInternals(): void
    {
        $tachycardia = new Tachycardia();
        $tachycardia->executeBeforeFirstTest();
        $tachycardia->executeAfterSuccessfulTest(__METHOD__, 2.5);

        ob_start();
        $tachycardia->executeAfterLastTest();
        $contents = (string) ob_get_clean();

        self::assertTrue($tachycardia->hasSlowTests());
        self::assertSame([['label' => __METHOD__, 'time' => 2.5, 'limit' => 1.0]], $tachycardia->getSlowTests());
        self::assertStringContainsString('identified this slow test:', $contents);
    }

    public function testDisabledRun(): void
    {
        $monitor = (string) getenv('TACHYCARDIA_MONITOR');
        putenv('TACHYCARDIA_MONITOR=disabled');

        $tachycardia = new Tachycardia();
        $tachycardia->executeBeforeFirstTest();
        $tachycardia->executeAfterSuccessfulTest(__METHOD__, 2.5);
        $tachycardia->executeAfterLastTest();

        self::assertFalse($tachycardia->hasSlowTests());
        self::assertSame([], $tachycardia->getSlowTests());

        putenv('' === $monitor ? 'TACHYCARDIA_MONITOR' : 'TACHYCARDIA_MONITOR=enabled');
    }

    public function testWithHiddenRows(): void
    {
        $tachycardia = new Tachycardia(['reportable' => 1]);
        $tachycardia->executeBeforeFirstTest();
        $tachycardia->executeAfterSuccessfulTest(__METHOD__, 2.5);
        $tachycardia->executeAfterSuccessfulTest(__CLASS__ . '::testSlowTest', 2);

        ob_start();
        $tachycardia->executeAfterLastTest();
        $contents = (string) ob_get_clean();

        self::assertCount(2, $tachycardia->getSlowTests());
        self::assertStringContainsString('...and 1 more test hidden from view.', $contents);
    }

    public function testWithTabulate(): void
    {
        $tachycardia = new Tachycardia(['tabulate' => true]);
        $tachycardia->executeBeforeFirstTest();
        $tachycardia->executeAfterSuccessfulTest(__METHOD__, 2.5);
        $tachycardia->executeAfterSuccessfulTest(__CLASS__ . '::testSlowestTest', 7225);

        ob_start();
        $tachycardia->executeAfterLastTest();
        $contents = (string) ob_get_clean();

        self::assertCount(2, $tachycardia->getSlowTests());
        self::assertStringContainsString('Test Case', $contents);
        self::assertStringContainsString('Time Consumed', $contents);
        self::assertStringContainsString('Time Limit', $contents);
        self::assertStringContainsString('02:00:25.0000', $contents);
    }

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