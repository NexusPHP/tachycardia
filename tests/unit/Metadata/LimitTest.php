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

namespace Nexus\PHPUnit\Tachycardia\Tests\Metadata;

use Nexus\PHPUnit\Tachycardia\Metadata\NoTimeLimitForClass;
use Nexus\PHPUnit\Tachycardia\Metadata\NoTimeLimitForMethod;
use Nexus\PHPUnit\Tachycardia\Metadata\TimeLimitForClass;
use Nexus\PHPUnit\Tachycardia\Metadata\TimeLimitForMethod;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(NoTimeLimitForClass::class)]
#[CoversClass(NoTimeLimitForMethod::class)]
#[CoversClass(TimeLimitForClass::class)]
#[CoversClass(TimeLimitForMethod::class)]
final class LimitTest extends TestCase
{
    public function testLimitMetadataTimeLimits(): void
    {
        $a = new NoTimeLimitForClass();
        $b = new NoTimeLimitForMethod();
        $c = new TimeLimitForClass(2.5);
        $d = new TimeLimitForMethod(1.0);

        self::assertFalse($a->hasTimeLimit());
        self::assertFalse($b->hasTimeLimit());
        self::assertTrue($c->hasTimeLimit());
        self::assertTrue($d->hasTimeLimit());

        self::assertSame(0.0, $a->getTimeLimit()->asFloat());
        self::assertSame(0.0, $b->getTimeLimit()->asFloat());
        self::assertSame(2.5, $c->getTimeLimit()->asFloat());
        self::assertSame(1.0, $d->getTimeLimit()->asFloat());
    }

    public function testLimitMetadataImportance(): void
    {
        $a = new NoTimeLimitForClass();
        $b = new NoTimeLimitForMethod();
        $c = new TimeLimitForClass(2.5);
        $d = new TimeLimitForClass(2.0);
        $e = new TimeLimitForMethod(1.5);
        $f = new TimeLimitForMethod(1.0);

        self::assertFalse($a->isMoreImportantThan($b));
        self::assertTrue($a->isMoreImportantThan($c));
        self::assertTrue($a->isMoreImportantThan($d));
        self::assertTrue($a->isMoreImportantThan($e));
        self::assertTrue($a->isMoreImportantThan($f));

        self::assertTrue($b->isMoreImportantThan($a));
        self::assertTrue($b->isMoreImportantThan($c));
        self::assertTrue($b->isMoreImportantThan($d));
        self::assertTrue($b->isMoreImportantThan($e));
        self::assertTrue($b->isMoreImportantThan($f));

        self::assertFalse($c->isMoreImportantThan($a));
        self::assertFalse($c->isMoreImportantThan($b));
        self::assertFalse($c->isMoreImportantThan($d));
        self::assertFalse($c->isMoreImportantThan($e));
        self::assertFalse($c->isMoreImportantThan($f));

        self::assertFalse($d->isMoreImportantThan($a));
        self::assertFalse($d->isMoreImportantThan($b));
        self::assertTrue($d->isMoreImportantThan($c));
        self::assertFalse($d->isMoreImportantThan($e));
        self::assertFalse($d->isMoreImportantThan($f));

        self::assertFalse($e->isMoreImportantThan($a));
        self::assertFalse($e->isMoreImportantThan($b));
        self::assertTrue($e->isMoreImportantThan($c));
        self::assertTrue($e->isMoreImportantThan($d));
        self::assertFalse($e->isMoreImportantThan($f));
    }
}
