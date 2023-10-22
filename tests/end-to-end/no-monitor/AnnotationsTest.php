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

namespace Nexus\PHPUnit\Tachycardia\Tests\EndToEnd\UsingAnnotations;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @timeLimit 2.0
 */
#[CoversNothing]
final class AnnotationsTest extends TestCase
{
    /**
     * This should be reported as slow using the class time limit.
     */
    public function testSlowTestUsesClassTimeLimit(): void
    {
        usleep(2500000); // 2.5 seconds
        $this->addToAssertionCount(1);
    }

    /**
     * This should not be reported as slow since this uses the method's time limit.
     *
     * @timeLimit 3.0
     */
    public function testSlowTestUsesMethodTimeLimit(): void
    {
        usleep(2500000); // 2.5 seconds
        $this->addToAssertionCount(1);
    }
}
