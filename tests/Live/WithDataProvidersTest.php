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

namespace Nexus\PHPUnit\Extension\Tests\Live;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class WithDataProvidersTest extends TestCase
{
    /**
     * Assert that these slow tests will be printed in the console report
     * as they have a custom time limit provided here.
     *
     * @dataProvider timeProvider
     *
     * @timeLimit 0.50
     */
    public function testSlowProvidedTestRespectsTimeLimit(int $time): void
    {
        usleep($time);
        self::assertTrue(true);
    }

    /**
     * @return iterable<float[]>
     */
    public function timeProvider(): iterable
    {
        foreach ([0.55, 0.60, 0.70, 0.80, 0.90] as $float) {
            yield [(int) ($float * 1000000)];
        }
    }
}
