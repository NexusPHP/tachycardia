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

/**
 * Inspired from https://github.com/sebastianbergmann/phpunit/blob/main/src/Metadata/Parser/Registry.php.
 *
 * @internal
 */
final class Registry
{
    private static ?Parser $instance = null;

    /**
     * @codeCoverageIgnore
     */
    private function __construct() {}

    public static function parser(): Parser
    {
        return self::$instance ??= self::build();
    }

    public static function reset(): void
    {
        self::$instance = null;
    }

    private static function build(): Parser
    {
        return new CachingParser(
            new ParserChain(
                new AttributeParser(),
                new AnnotationParser(),
            ),
        );
    }
}
