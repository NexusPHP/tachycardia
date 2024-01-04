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

use Nexus\PHPUnit\Tachycardia\SlowTest\SlowTestIdentifier;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(SlowTestIdentifier::class)]
final class SlowTestIdentifierTest extends TestCase
{
    public function testIdCannotBeEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('ID cannot be empty.');

        SlowTestIdentifier::from('', __FILE__);
    }

    public function testFileCannotBeGetcwd(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('File path cannot be the current working directory.');

        SlowTestIdentifier::from('Foo::bar', (string) getcwd());
    }

    #[DataProvider('provideLineCannotBeLessThanOneCases')]
    public function testLineCannotBeLessThanOne(int $line): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Line cannot be less than 1.');

        SlowTestIdentifier::from('Foo::bar', __FILE__, $line);
    }

    /**
     * @return iterable<string, list<int>>
     */
    public static function provideLineCannotBeLessThanOneCases(): iterable
    {
        yield 'minus one' => [-1];

        yield 'zero' => [0];
    }

    public function testFromReturnsProperties(): void
    {
        $identifier = SlowTestIdentifier::from('Foo::bar', __FILE__, 32);

        self::assertSame('Foo::bar', $identifier->id());
        self::assertSame('/tests/unit/SlowTest/SlowTestIdentifierTest.php', $identifier->file());
        self::assertSame(32, $identifier->line());
    }
}
