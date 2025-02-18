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

/**
 * @internal
 */
final class AnnotationParser implements Parser
{
    /**
     * @var array<class-string, array<non-empty-string, non-empty-list<string>>>
     */
    private array $classDocblocks = [];

    /**
     * @var array<class-string, array<non-empty-string, array<non-empty-string, non-empty-list<string>>>>
     */
    private array $methodDocblocks = [];

    public function forClass(string $className): LimitCollection
    {
        $limits = [];

        foreach ($this->parseClassName($className) as $annotation => $values) {
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

        foreach ($this->parseMethodName($className, $methodName) as $annotation => $values) {
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

    /**
     * @param class-string $class
     *
     * @return array<non-empty-string, non-empty-list<string>>
     */
    private function parseClassName(string $class): array
    {
        if (\array_key_exists($class, $this->classDocblocks)) {
            return $this->classDocblocks[$class];
        }

        $reflection = new \ReflectionClass($class);
        $annotations = array_merge(
            $this->parseDocComment((string) $reflection->getDocComment()),
            ...array_map(
                fn(\ReflectionClass $trait): array => $this->parseDocComment((string) $trait->getDocComment()),
                array_values($reflection->getTraits()),
            ),
        );

        $this->classDocblocks[$class] = $annotations;

        return $annotations;
    }

    /**
     * @param class-string     $class
     * @param non-empty-string $method
     *
     * @return array<non-empty-string, non-empty-list<string>>
     */
    private function parseMethodName(string $class, string $method): array
    {
        if (isset($this->methodDocblocks[$class][$method])) {
            return $this->methodDocblocks[$class][$method];
        }

        $reflection = new \ReflectionMethod($class, $method);
        $annotations = $this->parseDocComment((string) $reflection->getDocComment());

        $this->methodDocblocks[$class][$method] = $annotations;

        return $annotations;
    }

    /**
     * @return array<non-empty-string, non-empty-list<string>>
     */
    private function parseDocComment(string $docComment): array
    {
        $docComment = substr($docComment, 3, -2);
        $annotations = [];

        if (preg_match_all('/@(?P<name>[A-Za-z_-]+)(?:[ \t]+(?P<value>.*?))?[ \t]*\r?$/m', $docComment, $matches) > 0) {
            $numMatches = \count($matches[0]);

            for ($i = 0; $i < $numMatches; ++$i) {
                $annotations[$matches['name'][$i]][] = $matches['value'][$i];
            }
        }

        return $annotations;
    }
}
