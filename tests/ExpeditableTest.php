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

use Nexus\PHPUnit\Extension\Expeditable;
use PHPUnit\Framework\Constraint\IsType;
use PHPUnit\Framework\Constraint\LessThan;
use PHPUnit\Framework\Constraint\LogicalAnd;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \Nexus\PHPUnit\Extension\Expeditable
 */
final class ExpeditableTest extends TestCase
{
    use Expeditable;

    protected function setUp(): void
    {
        sleep(1);
    }

    protected function tearDown(): void
    {
        sleep(1);
    }

    public function testFastTest(): void
    {
        self::assertTrue(true);
    }

    /**
     * @depends testFastTest
     */
    public function testTraitEliminatesHookTimes(): void
    {
        $testName = md5('Nexus\PHPUnit\Extension\Tests\ExpeditableTest::testFastTest');
        self::assertTrue(isset($GLOBALS['__TACHYCARDIA_TIME_STATES'][$testName]));
        self::assertTrue(isset($GLOBALS['__TACHYCARDIA_TIME_STATES'][$testName]['bare']));
        self::assertThat(
            $GLOBALS['__TACHYCARDIA_TIME_STATES'][$testName]['bare'],
            LogicalAnd::fromConstraints(new IsType('float'), new LessThan(1.0)),
        );
    }
}
