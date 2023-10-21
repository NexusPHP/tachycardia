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
 * Inspired from https://github.com/sebastianbergmann/phpunit/blob/main/src/Metadata/Parser/CachingParser.php.
 *
 * @internal
 */
final class CachingParser implements Parser
{
    /**
     * @var array<class-string, LimitCollection>
     */
    private array $classCache = [];

    /**
     * @var array<string, LimitCollection>
     */
    private array $methodCache = [];

    /**
     * @var array<string, LimitCollection>
     */
    private array $classAndMethodCache = [];

    public function __construct(
        private readonly Parser $reader,
    ) {}

    public function forClass(string $className): LimitCollection
    {
        if (isset($this->classCache[$className])) {
            return $this->classCache[$className];
        }

        $this->classCache[$className] = $this->reader->forClass($className);

        return $this->classCache[$className];
    }

    public function forMethod(string $className, string $methodName): LimitCollection
    {
        $key = $className.'::'.$methodName;

        if (isset($this->methodCache[$key])) {
            return $this->methodCache[$key];
        }

        $this->methodCache[$key] = $this->reader->forMethod($className, $methodName);

        return $this->methodCache[$key];
    }

    public function forClassAndMethod(string $className, string $methodName): LimitCollection
    {
        $key = $className.'::'.$methodName;

        if (isset($this->classAndMethodCache[$key])) {
            return $this->classAndMethodCache[$key];
        }

        $this->classAndMethodCache[$key] = $this->forClass($className)->mergeWith(
            $this->forMethod($className, $methodName),
        );

        return $this->classAndMethodCache[$key];
    }
}
