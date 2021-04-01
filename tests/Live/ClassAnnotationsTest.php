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

namespace Nexus\PHPUnit\Extension\Tests\Live;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @timeLimit 2.0
 */
final class ClassAnnotationsTest extends TestCase
{
    /**
     * This should be reported as slow using the class time limit.
     *
     * @return void
     */
    public function testSlowTestUsesClassTimeLimit(): void
    {
        usleep(2500000); // 2.5 seconds
        self::assertTrue(true);
    }

    /**
     * This should not be reported as slow since this uses the method's time limit.
     *
     * @timeLimit 3.0
     *
     * @return void
     */
    public function testSlowTestUsesMethodTimeLimit(): void
    {
        usleep(2500000); // 2.5 seconds
        self::assertTrue(true);
    }
}
