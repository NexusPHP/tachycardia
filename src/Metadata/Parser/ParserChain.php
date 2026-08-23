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

namespace Nexus\PHPUnit\Tachycardia\Metadata\Parser;

use Nexus\PHPUnit\Tachycardia\Metadata\LimitCollection;

/**
 * Inspired from https://github.com/sebastianbergmann/phpunit/blob/main/src/Metadata/Parser/ParserChain.php.
 *
 * @internal
 */
final class ParserChain implements Parser
{
    public function __construct(
        private readonly Parser $attributeParser,
        private readonly Parser $annotationParser,
    ) {}

    public function forClass(string $className): LimitCollection
    {
        $limits = $this->attributeParser->forClass($className);

        if (! $limits->isEmpty()) {
            return $limits;
        }

        return $this->annotationParser->forClass($className);
    }

    public function forMethod(string $className, string $methodName): LimitCollection
    {
        $limits = $this->attributeParser->forMethod($className, $methodName);

        if (! $limits->isEmpty()) {
            return $limits;
        }

        return $this->annotationParser->forMethod($className, $methodName);
    }

    public function forClassAndMethod(string $className, string $methodName): LimitCollection
    {
        return $this->forClass($className)->mergeWith($this->forMethod($className, $methodName));
    }
}
