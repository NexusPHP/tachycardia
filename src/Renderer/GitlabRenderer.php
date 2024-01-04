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
 * @see https://docs.gitlab.com/ee/ci/testing/code_quality.html#implement-a-custom-tool
 */
final class GitlabRenderer implements CiRenderer
{
    use CreatesMessage;

    public function __construct(
        private readonly Precision $precision,
    ) {}

    public function runningInCi(): bool
    {
        return getenv('GITLAB_CI') !== false;
    }

    public function render(SlowTestCollection $collection, ?Info $telemetryInfo = null): string
    {
        $buffer = [];

        foreach ($collection as $slowTest) {
            $test = $slowTest->identifier();
            $message = $this->createMessage($slowTest);

            $buffer[] = [
                'description' => $message,
                'fingerprint' => hash('sha256', $message),
                'severity' => 'minor',
                'location' => [
                    'path' => $test->file(),
                    'lines' => [
                        'begin' => $test->line(),
                    ],
                ],
            ];
        }

        return json_encode($buffer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    }
}
