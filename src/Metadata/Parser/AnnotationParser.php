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
use Nexus\PHPUnit\Tachycardia\Metadata\NoTimeLimitForClass;
use Nexus\PHPUnit\Tachycardia\Metadata\NoTimeLimitForMethod;
use Nexus\PHPUnit\Tachycardia\Metadata\TimeLimitForClass;
use Nexus\PHPUnit\Tachycardia\Metadata\TimeLimitForMethod;
use PHPUnit\Metadata\Annotation\Parser\Registry as AnnotationRegistry;

/**
 * Inspired from https://github.com/sebastianbergmann/phpunit/blob/main/src/Metadata/Parser/AnnotationParser.php.
 *
 * @internal
 */
final class AnnotationParser implements Parser
{
    public function forClass(string $className): LimitCollection
    {
        $limits = [];

        foreach (AnnotationRegistry::getInstance()->forClassName($className)->symbolAnnotations() as $annotation => $values) {
            switch ($annotation) {
                case 'noTimeLimit':
                    $limits[] = new NoTimeLimitForClass();
                    break;

                case 'timeLimit':
                    $limits[] = new TimeLimitForClass((float) $values[0]);
                    break;
            }
        }

        return LimitCollection::fromArray($limits);
    }

    public function forMethod(string $className, string $methodName): LimitCollection
    {
        $limits = [];

        foreach (AnnotationRegistry::getInstance()->forMethod($className, $methodName)->symbolAnnotations() as $annotation => $values) {
            switch ($annotation) {
                case 'noTimeLimit':
                    $limits[] = new NoTimeLimitForMethod();
                    break;

                case 'timeLimit':
                    $limits[] = new TimeLimitForMethod((float) $values[0]);
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
