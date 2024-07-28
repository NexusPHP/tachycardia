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
use Nexus\PHPUnit\Tachycardia\SlowTest\SlowTest;

/**
 * @property Precision $precision
 */
trait CreatesMessage
{
    private function createMessage(SlowTest $slowTest): string
    {
        $precision = $this->precision->asInt();

        return \sprintf(
            "Took %.{$precision}fs from %.{$precision}fs limit to run %s",
            $slowTest->testTime()->asFloat(),
            $slowTest->limit()->asFloat(),
            addslashes($slowTest->identifier()->id()),
        );
    }
}
