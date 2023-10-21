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
 * Inspired from https://github.com/sebastianbergmann/phpunit/blob/main/src/Metadata/Parser/Parser.php.
 *
 * @internal
 */
interface Parser
{
    /**
     * @param class-string $className
     */
    public function forClass(string $className): LimitCollection;

    /**
     * @param class-string     $className
     * @param non-empty-string $methodName
     */
    public function forMethod(string $className, string $methodName): LimitCollection;

    /**
     * @param class-string     $className
     * @param non-empty-string $methodName
     */
    public function forClassAndMethod(string $className, string $methodName): LimitCollection;
}
