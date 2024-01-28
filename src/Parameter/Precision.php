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
final class Precision
{
    /**
     * @param int<1, max> $precision
     */
    private function __construct(
        private readonly int $precision,
    ) {}

    /**
     * @throws \InvalidArgumentException
     */
    public static function fromInt(int $precision): self
    {
        if ($precision <= 0) {
            throw new \InvalidArgumentException('Precision cannot be less than or equal to zero.');
        }

        return new self($precision);
    }

    /**
     * @return int<1, max>
     */
    public function asInt(): int
    {
        return $this->precision;
    }
}
