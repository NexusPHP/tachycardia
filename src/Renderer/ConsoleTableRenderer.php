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

final class ConsoleTableRenderer extends AbstractConsoleRenderer
{
    protected function renderBody(SlowTestCollection $collection): string
    {
        if ($collection->isEmpty()) {
            return '';
        }

        $slows = [];
        $precision = $this->precision->asInt();
        $slowTests = \array_slice($collection->asArray(), 0, $this->getReportable($collection));
        $max = ['id' => \strlen('Test Case'), 'time' => \strlen('Time Consumed'), 'limit' => \strlen('Time Limit')];

        foreach ($slowTests as $slowTest) {
            $id = addslashes($slowTest->identifier()->id());
            $time = $this->durationFormatter()->format($slowTest->testTime(), $precision);
            $limit = $this->durationFormatter()->format($slowTest->limit(), $precision);

            $max['id'] = max($max['id'], \strlen($id));
            $max['time'] = max($max['time'], \strlen($time));
            $max['limit'] = max($max['limit'], \strlen($limit));

            $slows[] = compact('id', 'time', 'limit');
        }

        foreach ($slows as $i => $row) {
            foreach ($max as $key => $length) {
                $slows[$i][$key] = $row[$key].str_repeat(' ', $length - \strlen($row[$key]));
            }
        }

        $table = '+';

        foreach ($max as $length) {
            $table .= str_repeat('-', $length + 2).'+';
        }

        $table .= "\n";
        $body = $footer = $table;

        $table .= \sprintf(
            "| %s | %s | %s |\n",
            $this->color()->colorize('Test Case', 'fg-green').str_repeat(' ', $max['id'] - \strlen('Test Case')),
            $this->color()->colorize('Time Consumed', 'fg-green').str_repeat(' ', $max['time'] - \strlen('Time Consumed')),
            $this->color()->colorize('Time Limit', 'fg-green').str_repeat(' ', $max['limit'] - \strlen('Time Limit')),
        );
        $table .= $body;

        foreach ($slows as ['id' => $id, 'time' => $time, 'limit' => $limit]) {
            $table .= \sprintf("| %s | %s | %s |\n", $id, $time, $limit);
        }

        $table .= $footer;

        return $table;
    }
}
