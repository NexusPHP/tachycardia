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
 * @noTimeLimit
 *
 * @internal
 *
 * @coversNothing
 */
final class NoTimeLimitInClassTest extends TestCase
{
    /**
     * This should not be reported as slow since
     * this is explicitly disabled.
     */
    public function testSlowTestDisabledForProfiling(): void
    {
        usleep(1500000);
        self::assertTrue(true);
    }
}
