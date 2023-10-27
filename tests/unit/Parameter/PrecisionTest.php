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

use Nexus\PHPUnit\Tachycardia\Parameter\Precision;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @noTimeLimit
 *
 * @internal
 */
#[CoversClass(Precision::class)]
final class PrecisionTest extends TestCase
{
    /**
     * @noTimeLimit
     */
    public function testPrecisionReturnsThePrecisionNumber(): void
    {
        self::assertSame(5, Precision::fromInt(5)->asInt());
    }

    #[DataProvider('providePrecisionThrowsExceptionOnNonPositiveIntsCases')]
    public function testPrecisionThrowsExceptionOnNonPositiveInts(int $precision): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Precision cannot be less than or equal to zero.');

        Precision::fromInt($precision);
    }

    /**
     * @return iterable<string, list<int>>
     */
    public static function providePrecisionThrowsExceptionOnNonPositiveIntsCases(): iterable
    {
        yield '0' => [0];

        yield '-1' => [-1];
    }
}
