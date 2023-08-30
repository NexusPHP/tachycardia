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

namespace Nexus\PHPUnit\Tachycardia\Metadata;

use PHPUnit\Event\Telemetry\Duration;

/**
 * @internal
 */
interface Limit
{
    public function hasTimeLimit(): bool;

    public function getTimeLimit(): Duration;

    public function isMoreImportantThan(self $other): bool;
}
