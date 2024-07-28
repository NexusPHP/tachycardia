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

final class RendererFactory
{
    /**
     * @var array<non-empty-string, class-string<Renderer>>
     */
    public const SUPPORTED_RENDERERS = [
        'list' => ConsoleListRenderer::class,
        'table' => ConsoleTableRenderer::class,
        ...self::SUPPORTED_CI_RENDERERS,
    ];

    /**
     * @var array<non-empty-string, class-string<CiRenderer>>
     */
    public const SUPPORTED_CI_RENDERERS = [
        'github' => GithubRenderer::class,
        'gitlab' => GitlabRenderer::class,
        'teamcity' => TeamCityRenderer::class,
    ];

    /**
     * @return ($format is key-of<self::SUPPORTED_CI_RENDERERS> ? CiRenderer : Renderer)
     *
     * @throws \AssertionError
     * @throws \InvalidArgumentException
     */
    public static function from(
        string $format,
        Precision $precision,
        ReportCount $reportCount,
        Color $color,
        DurationFormatter $durationFormatter,
    ): CiRenderer|Renderer {
        if (! isset(self::SUPPORTED_RENDERERS[$format])) {
            $knownFormats = array_keys(self::SUPPORTED_RENDERERS);
            $lastFormat = array_pop($knownFormats);

            throw new \InvalidArgumentException(\sprintf(
                'Invalid format "%s" given. Expected one of "%s%s".',
                $format,
                implode('", "', $knownFormats),
                '", and "'.$lastFormat,
            ));
        }

        $renderer = self::SUPPORTED_RENDERERS[$format];
        $instance = new $renderer($precision);

        if ($instance instanceof ReportCountAwareRenderer) {
            $instance->setReportCount($reportCount);
        }

        if ($instance instanceof ColorAwareRenderer) {
            $instance->setColor($color);
        }

        if ($instance instanceof DurationFormatterAwareRenderer) {
            $instance->setDurationFormatter($durationFormatter);
        }

        if (isset(self::SUPPORTED_CI_RENDERERS[$format])) {
            \assert($instance instanceof CiRenderer);
        }

        return $instance;
    }
}
