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

namespace Nexus\PHPUnit\Extension\Tests\Util;

use Nexus\PHPUnit\Extension\Tests\Live\NoTimeLimitInMethodTest;
use Nexus\PHPUnit\Extension\Tests\Live\SlowTestsTest;
use Nexus\PHPUnit\Extension\Util\TestCase as UtilTestCase;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \Nexus\PHPUnit\Extension\Util\TestCase
 */
final class TestCaseTest extends TestCase
{
    public function testGetters(): void
    {
        $testcase = new UtilTestCase(NoTimeLimitInMethodTest::class, 'testSlowTestDisabledForProfiling');

        self::assertSame(NoTimeLimitInMethodTest::class, $testcase->getClass());
        self::assertSame('testSlowTestDisabledForProfiling', $testcase->getName());
        self::assertSame('Nexus\PHPUnit\Extension\Tests\Live\NoTimeLimitInMethodTest::testSlowTestDisabledForProfiling', $testcase->getTestName());
    }

    public function testGetAnnotations(): void
    {
        $testcase = new UtilTestCase(NoTimeLimitInMethodTest::class, 'testSlowTestDisabledForProfiling');
        $annotations = [
            'method' => ['noTimeLimit' => ['']],
            'class' => [
                'internal' => [''],
                'coversNothing' => [''],
            ],
        ];

        self::assertSame($annotations, $testcase->getAnnotations());
        self::assertTrue($testcase->hasClassAnnotation('internal'));
        self::assertFalse($testcase->hasClassAnnotation('extends'));
        self::assertSame([''], $testcase->getClassAnnotation('internal'));
        self::assertTrue($testcase->hasMethodAnnotation('noTimeLimit'));
        self::assertFalse($testcase->hasMethodAnnotation('timeLimit'));
        self::assertSame([''], $testcase->getMethodAnnotation('noTimeLimit'));
    }

    public function testGetTestNameWithDataName(): void
    {
        $testcase = new UtilTestCase(SlowTestsTest::class, 'testWithProvider', ' with data set "slow"');

        self::assertSame('Nexus\PHPUnit\Extension\Tests\Live\SlowTestsTest::testWithProvider', $testcase->getTestName(false));
        self::assertSame('Nexus\PHPUnit\Extension\Tests\Live\SlowTestsTest::testWithProvider with data set "slow"', $testcase->getTestName(true));
    }
}
