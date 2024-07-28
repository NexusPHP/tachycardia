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

use Nexus\PHPUnit\Tachycardia\Console\Color;
use Nexus\PHPUnit\Tachycardia\DurationFormatter;
use Nexus\PHPUnit\Tachycardia\Parameter\Precision;
use Nexus\PHPUnit\Tachycardia\Parameter\ReportCount;
use Nexus\PHPUnit\Tachycardia\SlowTest\SlowTest;
use Nexus\PHPUnit\Tachycardia\SlowTest\SlowTestCollection;
use Nexus\PHPUnit\Tachycardia\TachycardiaExtension;
use PHPUnit\Event\Telemetry\Duration;
use PHPUnit\Event\Telemetry\Info;

abstract class AbstractConsoleRenderer implements ColorAwareRenderer, DurationFormatterAwareRenderer, Renderer, ReportCountAwareRenderer
{
    private ?Color $color = null;
    private ?DurationFormatter $durationFormatter = null;
    private ?ReportCount $reportCount = null;

    final public function __construct(
        protected readonly Precision $precision,
    ) {}

    final public function setColor(Color $color): self
    {
        $this->color = $color;

        return $this;
    }

    final public function setDurationFormatter(DurationFormatter $durationFormatter): DurationFormatterAwareRenderer
    {
        $this->durationFormatter = $durationFormatter;

        return $this;
    }

    final public function setReportCount(ReportCount $reportCount): self
    {
        $this->reportCount = $reportCount;

        return $this;
    }

    final public function render(SlowTestCollection $collection, ?Info $telemetryInfo = null): string
    {
        $buffer = '';

        $buffer .= $this->renderHeader($collection);
        $buffer .= $this->renderBody($collection);
        $buffer .= $this->renderFooter($collection, $telemetryInfo);

        return $buffer;
    }

    abstract protected function renderBody(SlowTestCollection $collection): string;

    protected function getReportable(SlowTestCollection $collection): int
    {
        return min($this->reportCount()->count(), $collection->count());
    }

    protected function color(): Color
    {
        \assert($this->color instanceof Color);

        return $this->color;
    }

    protected function durationFormatter(): DurationFormatter
    {
        \assert($this->durationFormatter instanceof DurationFormatter);

        return $this->durationFormatter;
    }

    protected function reportCount(): ReportCount
    {
        \assert($this->reportCount instanceof ReportCount);

        return $this->reportCount;
    }

    private function renderHeader(SlowTestCollection $collection): string
    {
        $slowCount = $this->getReportable($collection);

        if (0 === $slowCount) {
            return '';
        }

        return \sprintf(
            "\n\n%s identified %s %s:\n",
            $this->color()->colorize(TachycardiaExtension::class, 'fg-green'),
            1 === $slowCount ? 'this' : 'these',
            $this->color()->colorize(\sprintf(
                '%s slow %s',
                1 === $slowCount ? 'sole' : $slowCount,
                1 === $slowCount ? 'test' : 'tests',
            ), 'fg-yellow'),
        );
    }

    private function renderFooter(SlowTestCollection $collection, ?Info $telemetryInfo = null): string
    {
        if ($collection->isEmpty() || null === $telemetryInfo) {
            return '';
        }

        $slowTestsTime = array_sum(array_map(
            static fn(SlowTest $test): float => $test->testTime()->asFloat(),
            $collection->asArray(),
        ));

        $summary = \sprintf(
            "\nSlow tests: Time: %s (%.2f%%)",
            $this->formatDurationFromFloat($slowTestsTime),
            $slowTestsTime * 100 / $telemetryInfo->durationSinceStart()->asFloat(),
        );
        $hiddenTests = max($collection->count() - $this->reportCount()->count(), 0);

        if (0 === $hiddenTests) {
            return $summary;
        }

        return \sprintf(
            "...and %s hidden from view.\n%s",
            $this->color()->colorize(\sprintf('%s more %s', $hiddenTests, 1 === $hiddenTests ? 'test' : 'tests'), 'fg-yellow'),
            $summary,
        );
    }

    private function formatDurationFromFloat(float $duration): string
    {
        $seconds = (int) floor($duration);
        $nanoseconds = ($duration - $seconds) * 1_000_000_000;

        return $this->durationFormatter()->format(
            Duration::fromSecondsAndNanoseconds($seconds, (int) $nanoseconds),
            3,
        );
    }
}
