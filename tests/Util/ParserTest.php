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

use Nexus\PHPUnit\Extension\Util\Parser;
use Nexus\PHPUnit\Extension\Util\TestCase as UtilTestCase;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class ParserTest extends TestCase
{
    public function testInstanceIsSame(): void
    {
        $instance1 = Parser::getInstance();
        $instance2 = Parser::getInstance();

        self::assertInstanceOf(Parser::class, $instance1);
        self::assertInstanceOf(Parser::class, $instance2);
        self::assertSame($instance1, $instance2);
    }

    /**
     * @dataProvider nameProvider
     *
     * @param string $input
     *
     * @return void
     */
    public function testParsingYieldsTestCaseObject(string $input): void
    {
        $parser = Parser::getInstance();
        self::assertInstanceOf(UtilTestCase::class, $parser->parseTest($input));
    }

    /** @return iterable<array<string>> */
    public function nameProvider(): iterable
    {
        return [
            ['Nexus\PHPUnit\Extension\Tests\TachycardiaTest::testWithProvider with data set "slowest"'],
            ['Nexus\PHPUnit\Extension\Tests\TachycardiaTest::testWithProvider with data set "slower"'],
            ['Nexus\PHPUnit\Extension\Tests\TachycardiaTest::testWithProvider with data set "slow"'],
            ['Nexus\PHPUnit\Extension\Tests\TachycardiaTest::testSlowestTest'],
            ['Nexus\PHPUnit\Extension\Tests\TachycardiaTest::testSlowerTest'],
            ['Nexus\PHPUnit\Extension\Tests\TachycardiaTest::testSlowTest'],
            ['Nexus\PHPUnit\Extension\Tests\TachycardiaTest::testCustomLowerLimit'],
        ];
    }
}
