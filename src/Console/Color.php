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

namespace Nexus\PHPUnit\Tachycardia\Console;

/**
 * @internal
 */
final class Color
{
    private const COLORS = [
        'fg-black' => 30,
        'fg-red' => 31,
        'fg-green' => 32,
        'fg-yellow' => 33,
        'fg-blue' => 34,
        'fg-magenta' => 35,
        'fg-cyan' => 36,
        'fg-white' => 37,
        'fg-default' => 39,
        'bg-black' => 40,
        'bg-red' => 41,
        'bg-green' => 42,
        'bg-yellow' => 43,
        'bg-blue' => 44,
        'bg-magenta' => 45,
        'bg-cyan' => 46,
        'bg-white' => 47,
        'bg-default' => 49,
    ];
    private const BRIGHT_COLORS = [
        'fg-gray' => 90,
        'fg-bright-red' => 91,
        'fg-bright-green' => 92,
        'fg-bright-yellow' => 93,
        'fg-bright-blue' => 94,
        'fg-bright-magenta' => 95,
        'fg-bright-cyan' => 96,
        'fg-bright-white' => 97,
        'bg-gray' => 100,
        'bg-bright-red' => 101,
        'bg-bright-green' => 102,
        'bg-bright-yellow' => 103,
        'bg-bright-blue' => 104,
        'bg-bright-magenta' => 105,
        'bg-bright-cyan' => 106,
        'bg-bright-white' => 107,
    ];
    private const AVAILABLE_OPTIONS = [
        'bold' => ['set' => 1, 'unset' => 22],
        'underscore' => ['set' => 4, 'unset' => 24],
        'blink' => ['set' => 5, 'unset' => 25],
        'reverse' => ['set' => 7, 'unset' => 27],
        'conceal' => ['set' => 8, 'unset' => 28],
    ];

    public function __construct(
        private readonly bool $decorated,
    ) {}

    public function colorize(string $message, string $color): string
    {
        if (! $this->decorated || trim($message) === '') {
            return $message;
        }

        $colors = array_filter(
            array_map(trim(...), explode(',', $color)),
            static fn(string $color): bool => '' !== $color,
        );

        $setCodes = [];
        $unsetCodes = [];

        foreach ($colors as $colorCode) {
            if (isset(self::COLORS[$colorCode])) {
                $setCodes[] = self::COLORS[$colorCode];
                $unsetCodes[] = str_starts_with($colorCode, 'fg-') ? 39 : 49;

                continue;
            }

            if (isset(self::BRIGHT_COLORS[$colorCode])) {
                $setCodes[] = self::BRIGHT_COLORS[$colorCode];
                $unsetCodes[] = str_starts_with($colorCode, 'fg-') ? 39 : 49;

                continue;
            }

            if (isset(self::AVAILABLE_OPTIONS[$colorCode])) {
                $setCodes[] = self::AVAILABLE_OPTIONS[$colorCode]['set'];
                $unsetCodes[] = self::AVAILABLE_OPTIONS[$colorCode]['unset'];
            }
        }

        if ([] === $setCodes) {
            return $message;
        }

        return $this->optimizeColor(\sprintf(
            '%s%s%s',
            \sprintf("\033[%sm", implode(';', $setCodes)),
            $message,
            \sprintf("\033[%sm", implode(';', $unsetCodes)),
        ));
    }

    private function optimizeColor(string $buffer): string
    {
        return preg_replace(
            [
                "/\e\\[22m\e\\[2m/",
                "/\e\\[([^m]*)m\e\\[([1-9][0-9;]*)m/",
                "/(\e\\[[^m]*m)+(\e\\[0m)/",
            ],
            [
                '',
                "\e[$1;$2m",
                '$2',
            ],
            $buffer,
        ) ?? $buffer;
    }
}
