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

namespace Nexus\PHPUnit\Tachycardia\Parameter;

/**
 * @internal
 *
 * @immutable
 */
final class ReportCount
{
    /**
     * @param int<1, max> $count
     */
    private function __construct(
        private readonly int $count,
    ) {}

    /**
     * @throws \InvalidArgumentException
     */
    public static function from(int $count): self
    {
        if ($count <= 0) {
            throw new \InvalidArgumentException('Report count cannot be less than or equal to zero.');
        }

        return new self($count);
    }

    /**
     * @phpstan-return int<1, max>
     */
    public function count(): int
    {
        return $this->count;
    }
}
