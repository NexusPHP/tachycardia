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

use Nexus\PHPUnit\Tachycardia\Metadata\Parser\AnnotationParser;
use Nexus\PHPUnit\Tachycardia\Metadata\Parser\CachingParser;
use Nexus\PHPUnit\Tachycardia\Metadata\Parser\Parser;
use Nexus\PHPUnit\Tachycardia\Tests\Parameter\LimitTest;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(CachingParser::class)]
final class CachingAnnotationParserTest extends AbstractAnnotationParserTestCase
{
    public function testCachingParserReturnsSameInstance(): void
    {
        $parser = $this->parser();

        self::assertSame(
            $parser->forClass(LimitTest::class),
            $parser->forClass(LimitTest::class),
        );
        self::assertSame(
            $parser->forMethod(LimitTest::class, 'testLimitParameterReturnsTheSecondsAsDuration'),
            $parser->forMethod(LimitTest::class, 'testLimitParameterReturnsTheSecondsAsDuration'),
        );
        self::assertSame(
            $parser->forClassAndMethod(LimitTest::class, 'testLimitParameterReturnsTheSecondsAsDuration'),
            $parser->forClassAndMethod(LimitTest::class, 'testLimitParameterReturnsTheSecondsAsDuration'),
        );
    }

    protected function parser(): Parser
    {
        return new CachingParser(new AnnotationParser());
    }
}
