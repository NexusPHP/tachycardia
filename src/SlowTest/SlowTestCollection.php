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
 * @internal
 *
 * @implements \IteratorAggregate<int, SlowTest>
 */
final class SlowTestCollection implements \Countable, \IteratorAggregate
{
    /**
     * @var array<string, SlowTest>
     */
    private array $slowTests = [];

    /**
     * @phpstan-impure
     */
    public function push(SlowTest $slowTest): void
    {
        $id = $slowTest->identifier()->id();

        if (isset($this->slowTests[$id])) {
            $prev = $this->slowTests[$id];

            if ($slowTest->testTime()->isLessThan($prev->testTime())) {
                return;
            }
        }

        $this->slowTests[$id] = $slowTest;
    }

    /**
     * @phpstan-assert-if-true array{} $this->slowTests
     */
    public function isEmpty(): bool
    {
        return [] === $this->slowTests;
    }

    /**
     * @phpstan-impure
     *
     * @throws \RuntimeException
     */
    public function pop(): SlowTest
    {
        if ($this->isEmpty()) {
            throw new \RuntimeException('Collection is empty.');
        }

        return array_pop($this->slowTests);
    }

    /**
     * @return list<SlowTest>
     */
    public function asArray(): array
    {
        $slowTests = array_values($this->slowTests);

        usort($slowTests, static function (SlowTest $one, SlowTest $two): int {
            $durationOne = $one->testTime();
            $durationTwo = $two->testTime();

            if ($durationOne->isLessThan($durationTwo)) {
                return 1;
            }

            if ($durationOne->isGreaterThan($durationTwo)) {
                return -1;
            }

            return 0; // @codeCoverageIgnore
        });

        return $slowTests;
    }

    public function count(): int
    {
        return \count($this->slowTests);
    }

    public function getIterator(): SlowTestCollectionIterator
    {
        return new SlowTestCollectionIterator($this);
    }
}
