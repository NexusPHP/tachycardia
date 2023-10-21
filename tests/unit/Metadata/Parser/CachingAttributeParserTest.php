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

use Nexus\PHPUnit\Tachycardia\Metadata\Parser\AttributeParser;
use Nexus\PHPUnit\Tachycardia\Metadata\Parser\CachingParser;
use Nexus\PHPUnit\Tachycardia\Metadata\Parser\Parser;
use Nexus\PHPUnit\Tachycardia\Tests\Parameter\ReportCountTest;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(CachingParser::class)]
final class CachingAttributeParserTest extends AbstractAttributeParserTestCase
{
    public function testCachingParserReturnsSameInstance(): void
    {
        $parser = $this->parser();

        self::assertSame(
            $parser->forClass(ReportCountTest::class),
            $parser->forClass(ReportCountTest::class),
        );
        self::assertSame(
            $parser->forMethod(ReportCountTest::class, 'testReportCountReturnsReportCountInt'),
            $parser->forMethod(ReportCountTest::class, 'testReportCountReturnsReportCountInt'),
        );
        self::assertSame(
            $parser->forClassAndMethod(ReportCountTest::class, 'testReportCountReturnsReportCountInt'),
            $parser->forClassAndMethod(ReportCountTest::class, 'testReportCountReturnsReportCountInt'),
        );
    }

    protected function parser(): Parser
    {
        return new CachingParser(new AttributeParser());
    }
}
