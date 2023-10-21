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
use Nexus\PHPUnit\Tachycardia\Metadata\Parser\Parser;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(AttributeParser::class)]
final class AttributeParserTest extends AbstractAttributeParserTestCase
{
    protected function parser(): Parser
    {
        return new AttributeParser();
    }
}
