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

use Nexus\PHPUnit\Tachycardia\Parameter\ReportCount;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(ReportCount::class)]
final class ReportCountTest extends TestCase
{
    public function testReportCountReturnsReportCountInt(): void
    {
        self::assertSame(30, ReportCount::from(30)->count());
    }

    #[DataProvider('provideReportCountThrowsExceptionOnInvalidCountCases')]
    public function testReportCountThrowsExceptionOnInvalidCount(int $count): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Report count cannot be less than or equal to zero.');

        ReportCount::from($count);
    }

    /**
     * @return iterable<string, array<int, int>>
     */
    public static function provideReportCountThrowsExceptionOnInvalidCountCases(): iterable
    {
        yield '0' => [0];

        yield '-1' => [-1];
    }
}
