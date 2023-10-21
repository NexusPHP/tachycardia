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

namespace Nexus\PHPUnit\Tachycardia\Tests\Console;

use Nexus\PHPUnit\Tachycardia\Console\Color;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(Color::class)]
final class ColorTest extends TestCase
{
    public function testNotDecoratedColorReturnsMessageAsIs(): void
    {
        $color = new Color(false);

        self::assertSame('message', $color->colorize('message', 'fg-green'));
    }

    /**
     * @return iterable<string, list<string>>
     */
    public static function provideColoredMessageCases(): iterable
    {
        yield 'no message' => ['', 'fg-green', ''];

        yield 'no color' => ['message', '', 'message'];

        yield 'one color' => ['message', 'fg-blue', "\033[34mmessage\033[39m"];

        yield 'one bright color' => ['message', 'fg-bright-yellow', "\033[93mmessage\033[39m"];

        yield 'multiple colors' => ['message', 'bold,fg-white,bg-red', "\033[1;37;41mmessage\033[22;39;49m"];

        yield 'multiple bright colors' => ['message', 'bold,fg-bright-white,bg-bright-red', "\033[1;97;101mmessage\033[22;39;49m"];

        yield 'invalid color' => ['message', 'fg-foo', 'message'];

        yield 'invalid and valid colors' => ['message', 'fg-foo,bg-blue', "\033[44mmessage\033[49m"];
    }

    #[DataProvider('provideColoredMessageCases')]
    public function testColorize(string $message, string $color, string $expected): void
    {
        self::assertSame($expected, (new Color(true))->colorize($message, $color));
    }
}
