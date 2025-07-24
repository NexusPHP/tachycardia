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

namespace Nexus\PHPUnit\Tachycardia\Renderer;

use Nexus\PHPUnit\Tachycardia\Parameter\Precision;
use Nexus\PHPUnit\Tachycardia\SlowTest\SlowTestCollection;
use PHPUnit\Event\Telemetry\Info;

/**
 * @see https://www.jetbrains.com/help/teamcity/service-messages.html#Reporting+Inspections
 */
final class TeamCityRenderer implements CiRenderer
{
    use CreatesMessage;

    /**
     * @see https://www.jetbrains.com/help/teamcity/service-messages.html#Escaped+Values
     *
     * @var array<string, string>
     */
    private const ESCAPED_VALUES = [
        '\'' => '|\'',
        "\n" => '|n',
        "\r" => '|r',
        '|' => '||',
        '[' => '|[',
        ']' => '|]',
    ];

    public function __construct(
        private readonly Precision $precision,
    ) {}

    public function runningInCi(): bool
    {
        return getenv('TEAMCITY_VERSION') !== false;
    }

    public function render(SlowTestCollection $collection, ?Info $telemetryInfo = null): string
    {
        if ($collection->isEmpty()) {
            return '';
        }

        $buffer = self::createTeamcityLine('inspectionType', [
            'id' => 'tachycardia',
            'name' => 'tachycardia',
            'category' => 'tachycardia',
            'description' => 'tachycardia Inspection',
        ]);

        foreach ($collection as $slowTest) {
            $test = $slowTest->identifier();

            $buffer .= self::createTeamcityLine('inspection', [
                'typeId' => 'tachycardia',
                'message' => $this->createMessage($slowTest),
                'file' => $test->file(),
                'line' => $test->line(),
                'SEVERITY' => 'WARNING',
            ]);
        }

        return $buffer;
    }

    /**
     * Creates a Teamcity report line.
     *
     * @param array<string, int|string> $keyValuePairs The key=>value pairs
     */
    private static function createTeamcityLine(string $messageName, array $keyValuePairs): string
    {
        $string = '##teamcity['.$messageName;

        foreach ($keyValuePairs as $key => $value) {
            $string .= \sprintf(' %s=\'%s\'', $key, self::escape((string) $value));
        }

        return $string."]\n";
    }

    private static function escape(string $string): string
    {
        return strtr($string, self::ESCAPED_VALUES);
    }
}
