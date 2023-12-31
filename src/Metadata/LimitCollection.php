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

use Nexus\PHPUnit\Tachycardia\Parameter\Limit as LimitParameter;

/**
 * @internal
 *
 * @immutable
 *
 * @implements \IteratorAggregate<int, Limit>
 */
final class LimitCollection implements \Countable, \IteratorAggregate
{
    /**
     * @var list<Limit>
     */
    private readonly array $limits;

    private function __construct(Limit ...$limits)
    {
        $this->limits = array_values($limits);
    }

    /**
     * @param list<Limit> $limits
     */
    public static function fromArray(array $limits): self
    {
        return new self(...$limits);
    }

    /**
     * @return list<Limit>
     */
    public function asArray(): array
    {
        return $this->limits;
    }

    public function count(): int
    {
        return \count($this->limits);
    }

    public function isEmpty(): bool
    {
        return [] === $this->limits;
    }

    public function getIterator(): LimitCollectionIterator
    {
        return new LimitCollectionIterator($this);
    }

    public function mergeWith(self $other): self
    {
        return new self(...[
            ...$this->asArray(),
            ...$other->asArray(),
        ]);
    }

    public function reduce(LimitParameter $limitParameter): Limit
    {
        return array_reduce(
            $this->limits,
            static function (?Limit $initial, Limit $limit): Limit {
                if (null === $initial) {
                    return $limit;
                }

                if ($limit->isMoreImportantThan($initial)) {
                    return $limit;
                }

                return $initial; // @codeCoverageIgnore
            },
            null,
        ) ?? new TimeLimitForMethod($limitParameter->duration()->asFloat());
    }
}
