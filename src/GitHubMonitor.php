<?php

declare(strict_types=1);

/**
 * This file is part of NexusPHP Tachycardia.
 *
 * (c) 2021 John Paul E. Balandan, CPA <paulbalandan@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Nexus\PHPUnit\Extension;

final class GitHubMonitor
{
    /** @see https://github.com/actions/toolkit/blob/5e5e1b7aacba68a53836a34db4a288c3c1c1585b/packages/core/src/command.ts#L80-L85 */
    private const ESCAPED_DATA = [
        '%'  => '%25',
        "\r" => '%0D',
        "\n" => '%0A',
    ];

    /** @see https://github.com/actions/toolkit/blob/5e5e1b7aacba68a53836a34db4a288c3c1c1585b/packages/core/src/command.ts#L87-L94 */
    private const ESCAPED_PROPERTIES = [
        '%'  => '%25',
        "\r" => '%0D',
        "\n" => '%0A',
        ':'  => '%3A',
        ','  => '%2C',
    ];

    /**
     * Instance of Tachycardia;.
     *
     * @var Tachycardia
     */
    private $tachycardia;

    public function __construct(Tachycardia $tachycardia)
    {
        $this->tachycardia = $tachycardia;
    }

    public static function runningInGithubActions(): bool
    {
        return getenv('GITHUB_ACTIONS') !== false;
    }

    public function defibrillate(): void
    {
        foreach ($this->tachycardia->getSlowTests() as $test) {
            /** @phpstan-var class-string $class */
            [$class, $method] = explode('::', $test['label'], 2);
            $method = preg_replace('/^(test(?:\S+))(\s\S+)+/', '$1', $method) ?? '';

            try {
                $class = new \ReflectionClass($class);
                $method = $class->getMethod($method);
            } catch (\ReflectionException $e) {
                continue;
            }

            $file = (string) $class->getFileName();
            $file = str_replace((string) getcwd(), '', $file);
            $line = (int) $method->getStartLine();
            $message = $this->recreateMessage($test);

            $this->warning($message, $file, $line);
        }
    }

    /**
     * Output a warning using the Github annotations format.
     *
     * @param string $message
     * @param string $file
     * @param int    $line
     * @param int    $col
     *
     * @see https://docs.github.com/en/free-pro-team@latest/actions/reference/workflow-commands-for-github-actions#setting-a-warning-message
     */
    public function warning(string $message, string $file = '', int $line = 1, int $col = 0): void
    {
        $message = strtr($message, self::ESCAPED_DATA);

        if ('' === $file) {
            echo sprintf('::warning::%s', $message);

            return;
        }

        echo sprintf(
            '::warning file=%s, line=%s, col=%s::%s',
            strtr($file, self::ESCAPED_PROPERTIES),
            strtr((string) $line, self::ESCAPED_PROPERTIES),
            strtr((string) $col, self::ESCAPED_PROPERTIES),
            $message
        );
        echo "\n";
    }

    /**
     * Recreates the message given by Tachycardia::renderPlain without
     * the ANSI codes and check mark.
     *
     * @param array<string, mixed> $testDetails
     */
    private function recreateMessage(array $testDetails): string
    {
        ['label' => $label, 'time' => $time, 'limit' => $limit] = $testDetails;
        $precision = $this->tachycardia->getPrecision();

        return sprintf(
            "Took %s from %s limit to run %s\n",
            number_format($time, $precision) . 's',
            number_format($limit, $precision) . 's',
            $label
        );
    }
}