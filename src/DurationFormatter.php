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

namespace Nexus\PHPUnit\Tachycardia;

use PHPUnit\Event\Telemetry\Duration;

/**
 * @internal
 */
final class DurationFormatter
{
    private const PHPUNIT_DURATION_PRECISION = 9;

    /**
     * @throws \InvalidArgumentException
     */
    public function format(Duration $duration, int $precision): string
    {
        if ($precision <= 0) {
            throw new \InvalidArgumentException('Precision must be a positive int.');
        }

        $durationAsString = $duration->asString();

        if (self::PHPUNIT_DURATION_PRECISION === $precision) {
            return $durationAsString;
        }

        $chars = $precision + 3; // add 1 for decimal point and 2 for two digits

        return preg_replace_callback(
            '/^(\d{2}\:\d{2}\:)(\d{2}\.\d{9})$/',
            static fn(array $matches): string => $matches[1].\sprintf("%0{$chars}.{$precision}f", $matches[2]),
            $durationAsString,
        ) ?? $durationAsString;
    }
}
