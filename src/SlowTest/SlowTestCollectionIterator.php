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

namespace Nexus\PHPUnit\Tachycardia\SlowTest;

/**
 * @implements \Iterator<int, SlowTest>
 *
 * @internal
 */
final class SlowTestCollectionIterator implements \Iterator
{
    /**
     * @var list<SlowTest>
     */
    private readonly array $slowTests;

    private int $position = 0;

    public function __construct(SlowTestCollection $slowTestCollection)
    {
        $this->slowTests = $slowTestCollection->asArray();
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function valid(): bool
    {
        return $this->position < \count($this->slowTests);
    }

    public function key(): int
    {
        return $this->position;
    }

    public function current(): SlowTest
    {
        return $this->slowTests[$this->position];
    }

    public function next(): void
    {
        ++$this->position;
    }
}
