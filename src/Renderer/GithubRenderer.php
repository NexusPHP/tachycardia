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

final class GithubRenderer implements CiRenderer
{
    use CreatesMessage;

    /**
     * @var array<string, string>
     *
     * @see https://github.com/actions/toolkit/blob/5e5e1b7aacba68a53836a34db4a288c3c1c1585b/packages/core/src/command.ts#L80-L85
     */
    private const ESCAPED_DATA = [
        '%' => '%25',
        "\r" => '%0D',
        "\n" => '%0A',
    ];

    /**
     * @var array<string, string>
     *
     * @see https://github.com/actions/toolkit/blob/5e5e1b7aacba68a53836a34db4a288c3c1c1585b/packages/core/src/command.ts#L87-L94
     */
    private const ESCAPED_PROPERTIES = [
        '%' => '%25',
        "\r" => '%0D',
        "\n" => '%0A',
        ':' => '%3A',
        ',' => '%2C',
    ];

    public function __construct(
        private readonly Precision $precision,
    ) {}

    public function runningInCi(): bool
    {
        return getenv('GITHUB_ACTIONS') !== false;
    }

    public function render(SlowTestCollection $collection, ?Info $telemetryInfo = null): string
    {
        $buffer = '';

        foreach ($collection as $slowTest) {
            $test = $slowTest->identifier();

            $buffer .= $this->warning(
                $this->createMessage($slowTest),
                $test->file(),
                $test->line(),
            );
        }

        return $buffer;
    }

    /**
     * Output a warning using the Github annotations format.
     *
     * @see https://docs.github.com/en/free-pro-team@latest/actions/reference/workflow-commands-for-github-actions#setting-a-warning-message
     */
    private function warning(string $message, string $file, int $line = 1, int $col = 0): string
    {
        $message = strtr($message, self::ESCAPED_DATA);

        return \sprintf(
            "::warning file=%s,line=%d,col=%d::%s\n",
            strtr($file, self::ESCAPED_PROPERTIES),
            $line,
            $col,
            $message,
        );
    }
}
