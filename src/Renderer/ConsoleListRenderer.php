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

use Nexus\PHPUnit\Tachycardia\SlowTest\SlowTestCollection;

final class ConsoleListRenderer extends AbstractConsoleRenderer
{
    protected function renderBody(SlowTestCollection $collection): string
    {
        $buffer = '';

        if ($collection->isEmpty()) {
            return '';
        }

        $slowTests = \array_slice($collection->asArray(), 0, $this->getReportable($collection));
        $precision = $this->precision->asInt();

        foreach ($slowTests as $slowTest) {
            $buffer .= \sprintf(
                "%s  Took %s from %s limit to run %s\n",
                $this->color()->colorize("\xE2\x9A\xA0", 'fg-yellow'),
                $this->color()->colorize(\sprintf("%.{$precision}fs", $slowTest->testTime()->asFloat()), 'fg-yellow'),
                $this->color()->colorize(\sprintf("%.{$precision}fs", $slowTest->limit()->asFloat()), 'fg-yellow'),
                $this->color()->colorize(addslashes($slowTest->identifier()->id()), 'fg-green'),
            );
        }

        return $buffer;
    }
}
