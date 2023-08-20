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

namespace Nexus\PHPUnit\Tachycardia\Tests\Parameter;

use Nexus\PHPUnit\Tachycardia\Parameter\Limit;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(Limit::class)]
final class LimitTest extends TestCase
{
    public function testLimitParameterReturnsTheSecondsAsDuration(): void
    {
        $seconds = 2.50;

        self::assertSame($seconds, Limit::fromSeconds($seconds)->duration()->asFloat());
    }

    #[DataProvider('provideLimitParameterThrowsExceptionForInvalidSecondsCases')]
    public function testLimitParameterThrowsExceptionForInvalidSeconds(float $seconds): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Seconds cannot be less than or equal to zero.');

        Limit::fromSeconds($seconds);
    }

    /**
     * @return iterable<string, array<int, float>>
     */
    public static function provideLimitParameterThrowsExceptionForInvalidSecondsCases(): iterable
    {
        yield '0 seconds' => [0.0];

        yield '-2 seconds' => [-2.0];
    }
}
