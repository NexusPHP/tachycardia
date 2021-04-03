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

use Nexus\PHPUnit\Extension\ExpeditableTestCase;

/**
 * @internal
 */
final class ExpeditedTimeTest extends ExpeditableTestCase
{
    protected function setUp(): void
    {
        sleep(1);
    }

    public function testExpeditedTestButHookIsSlowIsNotReportedAsSlow(): void
    {
        self::assertTrue(true);
    }
}
