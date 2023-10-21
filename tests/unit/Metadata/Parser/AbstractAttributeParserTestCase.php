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

namespace Nexus\PHPUnit\Tachycardia\Tests\Metadata\Parser;

use Nexus\PHPUnit\Tachycardia\Metadata\Parser\Parser;
use Nexus\PHPUnit\Tachycardia\Parameter\Limit;
use Nexus\PHPUnit\Tachycardia\Tests\DurationFormatterTest;
use Nexus\PHPUnit\Tachycardia\Tests\Parameter\ReportCountTest;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

abstract class AbstractAttributeParserTestCase extends TestCase
{
    #[TestDox('#[TimeLimit] attribute on class')]
    final public function testTimeLimitAttributeOnClass(): void
    {
        $collection = $this->parser()->forClass(ReportCountTest::class);

        self::assertCount(1, $collection);
        self::assertTrue($collection->asArray()[0]->hasTimeLimit());
        self::assertSame(1.50, $collection->asArray()[0]->getTimeLimit()->asFloat());
    }

    #[TestDox('#[TimeLimit] attribute on method')]
    final public function testTimeLimitAttributeOnMethod(): void
    {
        $collection = $this->parser()->forMethod(ReportCountTest::class, 'testReportCountReturnsReportCountInt');

        self::assertCount(1, $collection);
        self::assertTrue($collection->asArray()[0]->hasTimeLimit());
        self::assertSame(0.75, $collection->asArray()[0]->getTimeLimit()->asFloat());
    }

    #[TestDox('#[TimeLimit] attribute on both class and method')]
    final public function testTimeLimitAttributeOnBothClassAndMethod(): void
    {
        $collection = $this->parser()->forClassAndMethod(ReportCountTest::class, 'testReportCountReturnsReportCountInt');

        self::assertCount(2, $collection);
        self::assertTrue($collection->asArray()[0]->hasTimeLimit());
        self::assertTrue($collection->asArray()[1]->hasTimeLimit());
        self::assertSame(1.50, $collection->asArray()[0]->getTimeLimit()->asFloat());
        self::assertSame(0.75, $collection->asArray()[1]->getTimeLimit()->asFloat());
        self::assertSame(0.75, $collection->reduce(Limit::fromSeconds(0.50))->getTimeLimit()->asFloat());
    }

    #[TestDox('#[NoTimeLimit] attribute on class')]
    final public function testNoTimeLimitAttributeOnClass(): void
    {
        $collection = $this->parser()->forClass(DurationFormatterTest::class);

        self::assertCount(1, $collection);
        self::assertFalse($collection->asArray()[0]->hasTimeLimit());
        self::assertSame(0.0, $collection->asArray()[0]->getTimeLimit()->asFloat());
    }

    #[TestDox('#[NoTimeLimit] attribute on method')]
    final public function testNoTimeLimitAttributeOnMethod(): void
    {
        $collection = $this->parser()->forMethod(DurationFormatterTest::class, 'testFormattingOfDuration');

        self::assertCount(1, $collection);
        self::assertFalse($collection->asArray()[0]->hasTimeLimit());
        self::assertSame(0.0, $collection->asArray()[0]->getTimeLimit()->asFloat());
    }

    #[TestDox('#[NoTimeLimit] attribute on both class and method')]
    final public function testNoTimeLimitAttributeOnBothClassAndMethod(): void
    {
        $collection = $this->parser()->forClassAndMethod(DurationFormatterTest::class, 'testFormattingOfDuration');

        self::assertCount(2, $collection);
        self::assertFalse($collection->asArray()[0]->hasTimeLimit());
        self::assertFalse($collection->asArray()[1]->hasTimeLimit());
    }

    abstract protected function parser(): Parser;
}
