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

/**
 * @internal
 *
 * @implements \Iterator<int, Limit>
 */
final class LimitCollectionIterator implements \Iterator
{
    /**
     * @var list<Limit>
     */
    private readonly array $limits;

    private int $position = 0;

    public function __construct(LimitCollection $limitCollection)
    {
        $this->limits = $limitCollection->asArray();
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function valid(): bool
    {
        return $this->position < \count($this->limits);
    }

    public function key(): int
    {
        return $this->position;
    }

    public function current(): Limit
    {
        return $this->limits[$this->position];
    }

    public function next(): void
    {
        ++$this->position;
    }
}
