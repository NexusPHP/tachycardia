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

use Nexus\PHPUnit\Tachycardia\Attribute\NoTimeLimit;
use Nexus\PHPUnit\Tachycardia\Attribute\TimeLimit;
use Nexus\PHPUnit\Tachycardia\Metadata\LimitCollection;
use Nexus\PHPUnit\Tachycardia\Metadata\NoTimeLimitForClass;
use Nexus\PHPUnit\Tachycardia\Metadata\NoTimeLimitForMethod;
use Nexus\PHPUnit\Tachycardia\Metadata\TimeLimitForClass;
use Nexus\PHPUnit\Tachycardia\Metadata\TimeLimitForMethod;

/**
 * Inspired from https://github.com/sebastianbergmann/phpunit/blob/main/src/Metadata/Parser/AttributeParser.php.
 *
 * @internal
 */
final class AttributeParser implements Parser
{
    public function forClass(string $className): LimitCollection
    {
        $limits = [];

        foreach ((new \ReflectionClass($className))->getAttributes() as $attribute) {
            if (! str_starts_with($attribute->getName(), 'Nexus\\PHPUnit\\Tachycardia\\Attribute\\')) {
                continue;
            }

            $attributeInstance = $attribute->newInstance();

            switch ($attribute->getName()) {
                case NoTimeLimit::class:
                    $limits[] = new NoTimeLimitForClass();
                    break;

                case TimeLimit::class:
                    \assert($attributeInstance instanceof TimeLimit);
                    $limits[] = new TimeLimitForClass($attributeInstance->seconds());
                    break;
            }
        }

        return LimitCollection::fromArray($limits);
    }

    public function forMethod(string $className, string $methodName): LimitCollection
    {
        $limits = [];

        foreach ((new \ReflectionMethod($className, $methodName))->getAttributes() as $attribute) {
            if (! str_starts_with($attribute->getName(), 'Nexus\\PHPUnit\\Tachycardia\\Attribute\\')) {
                continue;
            }

            $attributeInstance = $attribute->newInstance();

            switch ($attribute->getName()) {
                case NoTimeLimit::class:
                    $limits[] = new NoTimeLimitForMethod();
                    break;

                case TimeLimit::class:
                    \assert($attributeInstance instanceof TimeLimit);
                    $limits[] = new TimeLimitForMethod($attributeInstance->seconds());
                    break;
            }
        }

        return LimitCollection::fromArray($limits);
    }

    public function forClassAndMethod(string $className, string $methodName): LimitCollection
    {
        return $this->forClass($className)->mergeWith($this->forMethod($className, $methodName));
    }
}
