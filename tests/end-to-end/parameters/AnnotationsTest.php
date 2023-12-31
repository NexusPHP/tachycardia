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

namespace Nexus\PHPUnit\Tachycardia\Tests\EndToEnd\Parameters;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversNothing]
final class AnnotationsTest extends TestCase
{
    /**
     * This should be reported as slow using the class time limit.
     */
    public function testSlowTestUsesClassTimeLimit(): void
    {
        usleep(1000000); // 1.0 second
        $this->addToAssertionCount(1);
    }
}
