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

use PHPUnit\Runner\AfterLastTestHook;
use PHPUnit\Runner\AfterSuccessfulTestHook;
use PHPUnit\Runner\BeforeFirstTestHook;
use PHPUnit\Util\Test as TestUtil;

final class Tachycardia implements AfterLastTestHook, AfterSuccessfulTestHook, BeforeFirstTestHook
{
    /**
     * Whether this extension will check for slow tests.
     *
     * @var bool
     */
    private $monitor = true;

    /**
     * Default time limit in seconds.
     *
     * @var float
     */
    private $timeLimit = 1.00;

    /**
     * Number of reportable slow tests in console output.
     *
     * @var int
     */
    private $reportable = 10;

    /**
     * Degree of precision in the decimals of reported times.
     *
     * @var int
     */
    private $precision = 4;

    /**
     * Whether to tabulate the results instead of printing plainly.
     *
     * @var bool
     */
    private $tabulate = false;

    /**
     * Collection of tests which are slow.
     *
     * @var array<array{'label':string, 'time':float, 'limit':float}>
     */
    private $slowTests = [];

    /**
     * Internal count of test suites run. Returning to 0 means the tests
     * finished running.
     *
     * @var int
     */
    private $suites = 0;

    /**
     * Constructor.
     *
     * @param array{'timeLimit'?:float, 'reportable'?:int, 'precision'?:int, 'tabulate'?:bool} $options
     */
    public function __construct(array $options = [])
    {
        $this->monitor = getenv('TACHYCARDIA_MONITOR') !== 'disabled';
        $this->timeLimit = $options['timeLimit'] ?? 1.00;
        $this->reportable = $options['reportable'] ?? 10;
        $this->precision = $options['precision'] ?? 4;
        $this->tabulate = $options['tabulate'] ?? false;
    }

    public function executeAfterSuccessfulTest(string $test, float $time): void
    {
        if (! $this->monitor) {
            return;
        }

        $label = $this->getTestName($test);
        $limit = $this->parseTimeLimit($test);

        if ($time >= $limit) {
            $this->slowTests[] = compact('label', 'time', 'limit');
        }
    }

    public function executeBeforeFirstTest(): void
    {
        if (! $this->monitor) {
            return;
        }

        ++$this->suites;
    }

    public function executeAfterLastTest(): void
    {
        if (! $this->monitor) {
            return;
        }

        --$this->suites;

        if (0 === $this->suites && $this->hasSlowTests()) {
            usort($this->slowTests, static function ($a, $b): int {
                return $b['time'] <=> $a['time'];
            });

            $this->render();
            echo "\n";
        }

        if (GitHubMonitor::runningInGithubActions()) {
            $monitor = new GitHubMonitor($this);
            $monitor->defibrillate();
        }
    }

    public function hasSlowTests(): bool
    {
        return \count($this->slowTests) > 0;
    }

    /** @return array<array<mixed>> */
    public function getSlowTests()
    {
        return $this->slowTests;
    }

    public function getPrecision(): int
    {
        return $this->precision;
    }

    /**
     * Outputs the slow tests profiling into the console.
     *
     * This can be either via plain rendering or using
     * console tables.
     */
    public function render(): void
    {
        $this->renderHeader();

        if ($this->tabulate) {
            $this->renderAsTable();
        } else {
            $this->renderAsPlain();
        }

        $this->renderFooter();
    }

    private function renderAsTable(): void
    {
        $reportable = min($this->reportable, \count($this->slowTests));
        $slow = [];
        $max = ['label' => 9, 'time' => 13, 'limit' => 10];

        for ($index = 0; $index < $reportable; ++$index) {
            ['label' => $label, 'time' => $time, 'limit' => $limit] = $this->slowTests[$index];
            $time = $this->formTime($time);
            $limit = $this->formTime($limit);

            $max['label'] = max($max['label'], \strlen($label));
            $max['time'] = max($max['time'], \strlen($time));
            $max['limit'] = max($max['limit'], \strlen($limit));

            $slow[] = ['label' => $label, 'time' => $time, 'limit' => $limit];
        }

        foreach ($slow as $i => $row) {
            foreach ($max as $key => $length) {
                $slow[$i][$key] = $row[$key] . str_repeat(' ', $length - \strlen($row[$key]));
            }
        }

        $table = '+';

        foreach ($max as $length) {
            $table .= str_repeat('-', $length + 2) . '+';
        }

        $table .= "\n";
        $body = $footer = $table;

        $table .= sprintf(
            "| %s | %s | %s |\n",
            $this->color('Test Case', 'green') . str_repeat(' ', $max['label'] - 9),
            $this->color('Time Consumed', 'green') . str_repeat(' ', $max['time'] - 13),
            $this->color('Time Limit', 'green') . str_repeat(' ', $max['limit'] - 10)
        );
        $table .= $body;

        foreach ($slow as ['label' => $label, 'time' => $time, 'limit' => $limit]) {
            $table .= sprintf("| %s | %s | %s |\n", $label, $time, $limit);
        }

        $table .= $footer;

        echo $table;
    }

    private function renderAsPlain(): void
    {
        $reportable = min($this->reportable, \count($this->slowTests));

        for ($index = 0; $index < $reportable; ++$index) {
            ['label' => $label, 'time' => $time, 'limit' => $limit] = $this->slowTests[$index];

            echo sprintf(
                "%s  Took %s from %s limit to run %s\n",
                $this->color("\xE2\x9C\x94", 'bright_green'),
                $this->color(number_format($time, $this->precision) . 's', 'yellow'),
                $this->color(number_format($limit, $this->precision) . 's', 'yellow'),
                $this->color($label, 'green')
            );
        }
    }

    private function renderHeader(): void
    {
        $slow = \count($this->slowTests);

        echo sprintf(
            "\n\n%s identified %s slow %s:\n",
            $this->color(self::class, 'green'),
            1 === $slow ? 'this' : 'these',
            1 === $slow ? 'test' : 'tests'
        );
    }

    private function renderFooter(): void
    {
        $hiddenTests = max(\count($this->slowTests) - $this->reportable, 0);

        if ($hiddenTests > 0) {
            echo sprintf(
                "...and %s more %s hidden from view.\n",
                $hiddenTests,
                1 === $hiddenTests ? 'test' : 'tests'
            );
        }
    }

    /**
     * @param string $test A long description format of the current test
     *
     * @return string The test name without TestSuiteClassName:: and @dataprovider details
     */
    private function getTestName(string $test): string
    {
        $matches = [];

        if (preg_match('/^(?P<name>\S+::\S+)(?:(?P<dataname> with data set (?:#\d+|"[^"]+"))\s\()?/', $test, $matches) === 1) {
            $test = $matches['name'] . ($matches['dataname'] ?? '');
        }

        return $test;
    }

    private function parseTimeLimit(string $test): float
    {
        /** @phpstan-var class-string $class */
        [$class, $method] = explode('::', $this->getTestName($test), 2);
        $annotations = TestUtil::parseTestMethodAnnotations($class, $method);

        return isset($annotations['method']['timeLimit'][0]) && is_numeric($annotations['method']['timeLimit'][0])
            ? (float) $annotations['method']['timeLimit'][0] // @codeCoverageIgnore
            : $this->timeLimit;
    }

    private function color(string $text, string $color): string
    {
        static $colors = [
            'green'        => ['open' => 32, 'close' => 39],
            'yellow'       => ['open' => 33, 'close' => 39],
            'bright_green' => ['open' => 92, 'close' => 39],
        ];

        return sprintf(
            "\033[%sm%s\033[%sm",
            $colors[$color]['open'],
            $text,
            $colors[$color]['close']
        );
    }

    /**
     * Takes a timestamp given in `$seconds` and returns a string
     * in DD:HH:MM:SS form.
     *
     * @param float $seconds
     *
     * @return string
     */
    private function formTime(float $seconds): string
    {
        $second = fmod($seconds, 60);
        $second = number_format($second, $this->precision);

        if (preg_match('/^(\d+)(\.\d+)?/', $second, $matches) === 1) {
            $second = str_pad($matches[1], 2, '0', STR_PAD_LEFT) . ($matches[2] ?? '');
        }

        $minute = '00';
        $hour = '00';

        if ($seconds > 60) {
            $minute = str_pad((string) (int) (($seconds % 3600) / 60), 2, '0', STR_PAD_LEFT);
        }

        if ($seconds > 3600) {
            $hour = str_pad((string) (int) (($seconds % 86400) / 3600), 2, '0', STR_PAD_LEFT);
        }

        return sprintf('%s:%s:%s', $hour, $minute, $second);
    }
}
