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

namespace Nexus\PHPUnit\Tachycardia\Tests\SlowTest;

use Nexus\PHPUnit\Tachycardia\SlowTest\SlowTest;
use Nexus\PHPUnit\Tachycardia\SlowTest\SlowTestIdentifier;
use PHPUnit\Event\Telemetry\Duration;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(SlowTest::class)]
final class SlowTestTest extends TestCase
{
    public function testSlowTestAsValueObject(): void
    {
        $identifier = SlowTestIdentifier::from('Foo::bar', __FILE__);
        $testTime = Duration::fromSecondsAndNanoseconds(2, 350);
        $limit = Duration::fromSecondsAndNanoseconds(1, 0);

        $slowTest = new SlowTest($identifier, $testTime, $limit);
        self::assertSame($identifier, $slowTest->identifier());
        self::assertSame($testTime, $slowTest->testTime());
        self::assertSame($limit, $slowTest->limit());
    }
}
