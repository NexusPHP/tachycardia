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

namespace Nexus\PHPUnit\Tachycardia\Tests\Live;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class NoTimeLimitInMethodTest extends TestCase
{
    /**
     * This should be reported as slow.
     */
    public function testSlowTestNotDisabled(): void
    {
        usleep(1500000);
        self::assertTrue(true);
    }

    /**
     * This should not be reported as slow since
     * this is explicitly disabled.
     *
     * @noTimeLimit
     */
    public function testSlowTestDisabledForProfiling(): void
    {
        usleep(1500000);
        self::assertTrue(true);
    }
}
