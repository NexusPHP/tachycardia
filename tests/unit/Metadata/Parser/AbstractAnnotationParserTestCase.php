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
use Nexus\PHPUnit\Tachycardia\Tests\Parameter\LimitTest;
use Nexus\PHPUnit\Tachycardia\Tests\Parameter\PrecisionTest;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

abstract class AbstractAnnotationParserTestCase extends TestCase
{
    #[TestDox('@timeLimit annotation on class')]
    final public function testTimeLimitAnnotationOnClass(): void
    {
        $collection = $this->parser()->forClass(LimitTest::class);

        self::assertCount(1, $collection);
        self::assertTrue($collection->asArray()[0]->hasTimeLimit());
        self::assertSame(2.0, $collection->asArray()[0]->getTimeLimit()->asFloat());
    }

    #[TestDox('@timeLimit annotation on method')]
    final public function testTimeLimitAnnotationOnMethod(): void
    {
        $collection = $this->parser()->forMethod(LimitTest::class, 'testLimitParameterReturnsTheSecondsAsDuration');

        self::assertCount(1, $collection);
        self::assertTrue($collection->asArray()[0]->hasTimeLimit());
        self::assertSame(2.5, $collection->asArray()[0]->getTimeLimit()->asFloat());
    }

    #[TestDox('@timeLimit annotation on both class and method')]
    final public function testTimeLimitAnnotationOnBothClassAndMethod(): void
    {
        $collection = $this->parser()->forClassAndMethod(LimitTest::class, 'testLimitParameterReturnsTheSecondsAsDuration');

        self::assertCount(2, $collection);
        self::assertTrue($collection->asArray()[0]->hasTimeLimit());
        self::assertTrue($collection->asArray()[1]->hasTimeLimit());
        self::assertSame(2.0, $collection->asArray()[0]->getTimeLimit()->asFloat());
        self::assertSame(2.5, $collection->asArray()[1]->getTimeLimit()->asFloat());
        self::assertSame(2.5, $collection->reduce(Limit::fromSeconds(1.5))->getTimeLimit()->asFloat());
    }

    #[TestDox('@noTimeLimit annotation on class')]
    final public function testNoTimeLimitAnnotationOnClass(): void
    {
        $collection = $this->parser()->forClass(PrecisionTest::class);

        self::assertCount(1, $collection);
        self::assertFalse($collection->asArray()[0]->hasTimeLimit());
        self::assertSame(0.0, $collection->asArray()[0]->getTimeLimit()->asFloat());
    }

    #[TestDox('@noTimeLimit annotation on method')]
    final public function testNoTimeLimitAnnotationOnMethod(): void
    {
        $collection = $this->parser()->forMethod(PrecisionTest::class, 'testPrecisionReturnsThePrecisionNumber');

        self::assertCount(1, $collection);
        self::assertFalse($collection->asArray()[0]->hasTimeLimit());
        self::assertSame(0.0, $collection->asArray()[0]->getTimeLimit()->asFloat());
    }

    #[TestDox('@noTimeLimit annotation for both class and method')]
    final public function testNoTimeLimitAnnotationOnBothClassAndMethod(): void
    {
        $collection = $this->parser()->forClassAndMethod(PrecisionTest::class, 'testPrecisionReturnsThePrecisionNumber');

        self::assertCount(2, $collection);
        self::assertFalse($collection->asArray()[0]->hasTimeLimit());
        self::assertFalse($collection->asArray()[1]->hasTimeLimit());
        self::assertSame(0.0, $collection->asArray()[0]->getTimeLimit()->asFloat());
        self::assertSame(0.0, $collection->asArray()[1]->getTimeLimit()->asFloat());
        self::assertSame(0.0, $collection->reduce(Limit::fromSeconds(1.5))->getTimeLimit()->asFloat());
    }

    abstract protected function parser(): Parser;
}
