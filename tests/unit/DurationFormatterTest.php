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

namespace Nexus\PHPUnit\Tachycardia\Tests;

use Nexus\PHPUnit\Tachycardia\DurationFormatter;
use PHPUnit\Event\Telemetry\Duration;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(DurationFormatter::class)]
final class DurationFormatterTest extends TestCase
{
    #[DataProvider('provideFormattingOfDurationCases')]
    public function testFormattingOfDuration(Duration $duration, int $precision, string $expectedFormat): void
    {
        self::assertSame($expectedFormat, (new DurationFormatter())->format($duration, $precision));
    }

    /**
     * @return iterable<string, array{0: Duration, 1: int, 2: string}>
     */
    public static function provideFormattingOfDurationCases(): iterable
    {
        $duration = Duration::fromSecondsAndNanoseconds(1, 120_578_645);

        yield 'precision of 9 digits' => [$duration, 9, '00:00:01.120578645'];

        yield 'precision of 8 digits' => [$duration, 8, '00:00:01.12057864'];

        yield 'precision of 7 digits' => [$duration, 7, '00:00:01.1205786'];

        yield 'precision of 6 digits' => [$duration, 6, '00:00:01.120579'];

        yield 'precision of 5 digits' => [$duration, 5, '00:00:01.12058'];

        yield 'precision of 4 digits' => [$duration, 4, '00:00:01.1206'];

        yield 'precision of 3 digits' => [$duration, 3, '00:00:01.121'];

        yield 'precision of 2 digits' => [$duration, 2, '00:00:01.12'];

        yield 'precision of 1 digits' => [$duration, 1, '00:00:01.1'];
    }

    #[DataProvider('provideInvalidPrecisionThrowsExceptionCases')]
    public function testInvalidPrecisionThrowsException(int $precision): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Precision must be a positive int.');

        (new DurationFormatter())->format(Duration::fromSecondsAndNanoseconds(1, 1_000_000), $precision);
    }

    /**
     * @return iterable<string, array<int, int>>
     */
    public static function provideInvalidPrecisionThrowsExceptionCases(): iterable
    {
        yield '0' => [0];

        yield '-1' => [-1];
    }
}
