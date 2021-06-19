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

namespace Nexus\PHPUnit\Extension\Tests;

use Nexus\PHPUnit\Extension\Tachycardia;
use Nexus\PHPUnit\Extension\Tests\Live\ClassAnnotationsTest;
use Nexus\PHPUnit\Extension\Tests\Live\SlowTestsTest;
use Nexus\PHPUnit\Extension\Util\GithubMonitor;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \Nexus\PHPUnit\Extension\Tachycardia
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

        self::assertIsInt($tachycardia->getPrecision());
        self::assertTrue($tachycardia->hasSlowTests());
        self::assertSame([['label' => __METHOD__, 'time' => 2.5, 'limit' => 1.0]], $tachycardia->getSlowTests());
        self::assertStringContainsString('identified this sole slow test:', preg_replace('/\\033\[[^m]+m/', '', $contents) ?? '');
    }

    public function testDisabledRun(): void
    {
        $monitor = (string) getenv('TACHYCARDIA_MONITOR');
        $monitorGa = (string) getenv('TACHYCARDIA_MONITOR_GA');
        putenv('TACHYCARDIA_MONITOR=disabled');
        putenv('TACHYCARDIA_MONITOR_GA');

        $tachycardia = new Tachycardia();
        $tachycardia->executeBeforeFirstTest();
        $tachycardia->executeAfterSuccessfulTest(__METHOD__, 2.5);
        $tachycardia->executeAfterLastTest();

        self::assertFalse($tachycardia->hasSlowTests());
        self::assertSame([], $tachycardia->getSlowTests());

        putenv('' === $monitor ? 'TACHYCARDIA_MONITOR' : 'TACHYCARDIA_MONITOR=enabled');
        putenv('' === $monitorGa ? 'TACHYCARDIA_MONITOR_GA' : 'TACHYCARDIA_MONITOR_GA=enabled');
    }

    public function testWithHiddenRows(): void
    {
        $tachycardia = new Tachycardia(['reportable' => 1]);
        $tachycardia->executeBeforeFirstTest();
        $tachycardia->executeAfterSuccessfulTest(__METHOD__, 2.5);
        $tachycardia->executeAfterSuccessfulTest(SlowTestsTest::class . '::testSlowTest', 2);

        ob_start();
        $tachycardia->executeAfterLastTest();
        $contents = (string) ob_get_clean();

        self::assertCount(2, $tachycardia->getSlowTests());
        self::assertStringContainsString('...and 1 more test hidden from view.', preg_replace('/\\033\[[^m]+m/', '', $contents) ?? '');
    }

    public function testWithTabulate(): void
    {
        $tachycardia = new Tachycardia(['tabulate' => true, 'collectBare' => true]);
        $tachycardia->executeBeforeFirstTest();
        $tachycardia->executeAfterSuccessfulTest(__METHOD__, 2.5);
        $tachycardia->executeAfterSuccessfulTest(SlowTestsTest::class . '::testSlowestTest', 7225);

        ob_start();
        $tachycardia->executeAfterLastTest();
        $contents = (string) ob_get_clean();

        self::assertCount(2, $tachycardia->getSlowTests());
        self::assertStringContainsString('Test Case', $contents);
        self::assertStringContainsString('Time Consumed', $contents);
        self::assertStringContainsString('Time Limit', $contents);
        self::assertStringContainsString('02:00:25.0000', $contents);
    }

    /**
     * @covers \Nexus\PHPUnit\Extension\Util\GithubMonitor
     */
    public function testWithGithubActionReporting(): void
    {
        if (! GithubMonitor::runningInGithubActions()) {
            self::markTestSkipped('This should be tested in Github Actions.');
        }

        $monitorGa = (string) getenv('TACHYCARDIA_MONITOR_GA');
        putenv('TACHYCARDIA_MONITOR_GA=enabled');

        $tachycardia = new Tachycardia();
        $tachycardia->executeBeforeFirstTest();
        $tachycardia->executeAfterSuccessfulTest(__METHOD__, 2.5);

        ob_start();
        $tachycardia->executeAfterLastTest();
        $contents = (string) ob_get_clean();

        self::assertTrue($tachycardia->hasSlowTests());
        self::assertStringContainsString('::warning', $contents);

        putenv('' === $monitorGa ? 'TACHYCARDIA_MONITOR_GA' : 'TACHYCARDIA_MONITOR_GA=enabled');
    }

    public function testFullRun(): void
    {
        $tachycardia = new Tachycardia();
        $tachycardia->executeBeforeFirstTest();
        $tachycardia->executeAfterSuccessfulTest(__CLASS__ . '::testInternals', 2.5);
        $tachycardia->executeAfterSuccessfulTest(SlowTestsTest::class . '::testCustomLowerLimit', 1.1);
        $tachycardia->executeAfterSuccessfulTest(ClassAnnotationsTest::class . '::testSlowTestUsesClassTimeLimit', 2.5);

        ob_start();
        $tachycardia->executeAfterLastTest();
        ob_end_clean();

        self::assertSame([
            [
                'label' => __CLASS__ . '::testInternals',
                'time' => 2.5,
                'limit' => 1.0,
            ],
            [
                'label' => ClassAnnotationsTest::class . '::testSlowTestUsesClassTimeLimit',
                'time' => 2.5,
                'limit' => 2.0,
            ],
            [
                'label' => SlowTestsTest::class . '::testCustomLowerLimit',
                'time' => 1.1,
                'limit' => 0.5,
            ],
        ], $tachycardia->getSlowTests());
    }
}
