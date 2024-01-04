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

use Nexus\PHPUnit\Tachycardia\Console\Color;
use Nexus\PHPUnit\Tachycardia\Parameter\Limit;
use Nexus\PHPUnit\Tachycardia\Parameter\Precision;
use Nexus\PHPUnit\Tachycardia\Parameter\ReportCount;
use Nexus\PHPUnit\Tachycardia\Renderer\RendererFactory;
use Nexus\PHPUnit\Tachycardia\Renderer\RendererQueue;
use Nexus\PHPUnit\Tachycardia\SlowTest\SlowTestCollection;
use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;

final class TachycardiaExtension implements Extension
{
    private const DEFAULT_TIME_LIMIT = 1.00;
    private const DEFAULT_REPORT_COUNT = 10;
    private const DEFAULT_PRECISION = 4;
    private const DEFAULT_FORMAT = 'list';
    private const DEFAULT_CI_FORMAT = 'github';

    public function bootstrap(Configuration $configuration, Facade $facade, ParameterCollection $parameters): void
    {
        if ($configuration->noOutput()) {
            return;
        }

        $monitor = getenv('TACHYCARDIA_MONITOR') !== 'disabled';
        $monitorForGa = getenv('TACHYCARDIA_MONITOR_GA') === 'enabled';

        if (! $monitor && ! $monitorForGa) {
            return;
        }

        $limit = Limit::fromSeconds(self::DEFAULT_TIME_LIMIT);
        $count = ReportCount::from(self::DEFAULT_REPORT_COUNT);
        $precision = Precision::fromInt(self::DEFAULT_PRECISION);
        $format = self::DEFAULT_FORMAT;
        $ciFormat = self::DEFAULT_CI_FORMAT;

        if ($parameters->has('time-limit')) {
            $limit = Limit::fromSeconds((float) $parameters->get('time-limit'));
        }

        if ($parameters->has('report-count')) {
            $count = ReportCount::from((int) $parameters->get('report-count'));
        }

        if ($parameters->has('precision')) {
            $precision = Precision::fromInt((int) $parameters->get('precision'));
        }

        if ($parameters->has('format')) {
            $format = $parameters->get('format');
        }

        if ($parameters->has('ci-format')) {
            $ciFormat = $parameters->get('ci-format');
        }

        $color = new Color($configuration->colors());
        $stopwatch = new Stopwatch();
        $durationFormatter = new DurationFormatter();
        $collection = new SlowTestCollection();

        $facade->registerSubscribers(
            new Subscriber\Test\PreparedSubscriber($stopwatch),
            new Subscriber\Test\FinishedSubscriber($collection, $stopwatch, $limit),
            new Subscriber\TestRunner\ExecutionFinishedSubscriber(
                $collection,
                new RendererQueue(
                    RendererFactory::from($format, $precision, $count, $color, $durationFormatter),
                    RendererFactory::from($ciFormat, $precision, $count, $color, $durationFormatter),
                    $monitor,
                    $monitorForGa,
                ),
            ),
        );
    }
}
