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

use Nexus\PHPUnit\Tachycardia\Metadata\LimitCollection;
use Nexus\PHPUnit\Tachycardia\Metadata\Parser\AnnotationParser;
use Nexus\PHPUnit\Tachycardia\Metadata\Parser\Parser;
use Nexus\PHPUnit\Tachycardia\Metadata\Parser\ParserChain;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @internal
 */
#[CoversClass(ParserChain::class)]
final class ChainedAnnotationParserTest extends AbstractAnnotationParserTestCase
{
    protected function parser(): Parser
    {
        /** @var MockObject&Parser $attributeParser */
        $attributeParser = $this->createMock(Parser::class);

        $attributeParser->method('forClass')->willReturn(LimitCollection::fromArray([]));
        $attributeParser->method('forMethod')->willReturn(LimitCollection::fromArray([]));
        $attributeParser->method('forClassAndMethod')->willReturn(LimitCollection::fromArray([]));

        return new ParserChain($attributeParser, new AnnotationParser());
    }
}
