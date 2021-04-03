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

namespace Nexus\PHPUnit\Extension\Tests\Util;

use Nexus\PHPUnit\Extension\Tests\Live\SlowTestsTest;
use Nexus\PHPUnit\Extension\Util\TimeState;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class TimeStateTest extends TestCase
{
    /** @var string */
    private $test1;

    /** @var string */
    private $test2;

    /** @var string */
    private $test3;

    /** @var string */
    private $test4;

    /** @var array<string, array<string, float>> */
    private $states;

    protected function setUp(): void
    {
        $this->test1 = sprintf('%s::%s', SlowTestsTest::class, 'testFastTest');
        $this->test2 = sprintf('%s::%s', SlowTestsTest::class, 'testWithProvider with data set "slow"');
        $this->test3 = sprintf('%s::%s', SlowTestsTest::class, 'testWithProvider with data set "slower"');
        $this->test4 = sprintf('%s::%s', SlowTestsTest::class, 'testWithProvider with data set "slowest"');

        $this->states = $GLOBALS['__TACHYCARDIA_TIME_STATES'] = [
            md5($this->test1) => ['bare' => 0.5],
            md5($this->test2) => ['bare' => 5.0],
            md5($this->test3) => [],
        ];
    }

    protected function tearDown(): void
    {
        if (isset($GLOBALS['__TACHYCARDIA_TIME_STATES'])) {
            unset($GLOBALS['__TACHYCARDIA_TIME_STATES']);
        }
    }

    public function testTimeStateAcceptsNonEmptyArrayAsIs(): void
    {
        $array = [$this->test1 => ['bare' => 1.0]];
        $timeState = new TimeState($array);

        self::assertInstanceOf(TimeState::class, $timeState);
        self::assertSame($array, $timeState->retrieve());
    }

    public function testEmptyArrayParamGivesGlobals(): void
    {
        $timeState = new TimeState();
        self::assertSame($this->states, $timeState->retrieve());
    }

    public function testNonEmptyGlobalsDefaultsToArray(): void
    {
        $GLOBALS['__TACHYCARDIA_TIME_STATES'] = false;
        $timeState = new TimeState();
        self::assertSame([], $timeState->retrieve());
    }

    public function testFindUnknownTest(): void
    {
        self::assertNull((new TimeState())->find($this->test4));
    }

    public function testFindLoggedTests(): void
    {
        $timeState = new TimeState();

        self::assertSame(0.5, $timeState->find($this->test1, 1.0));
        self::assertIsArray($timeState->find($this->test1));
        self::assertSame(5.0, $timeState->find($this->test2 . ' (', 1.0));
        self::assertIsArray($timeState->find($this->test2 . ' ('));
        self::assertSame(1.0, $timeState->find($this->test3 . ' (', 1.0));
        self::assertSame(['actual' => 1.0], $timeState->find($this->test3 . ' ('));
    }
}
