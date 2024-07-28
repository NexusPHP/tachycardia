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

use PHPUnit\Event\Code\Phpt;
use PHPUnit\Event\Code\Test;
use PHPUnit\Event\Code\TestMethod;

/**
 * Identifier for a slow test.
 *
 * This class replaces the readonly `PHPUnit\Event\Code\Test`.
 *
 * @internal
 *
 * @immutable
 */
final class SlowTestIdentifier
{
    /**
     * @param non-empty-string $id
     * @param non-empty-string $file
     * @param int<1, max>      $line
     */
    private function __construct(
        private readonly string $id,
        private readonly string $file,
        private readonly int $line,
    ) {}

    /**
     * @throws \InvalidArgumentException
     */
    public static function fromTest(Test $test): self
    {
        if ($test instanceof TestMethod) {
            return self::from($test->id(), $test->file(), $test->line());
        }

        // @codeCoverageIgnoreStart
        if ($test instanceof Phpt) {
            return self::from($test->id(), $test->file());
        }

        throw new \InvalidArgumentException(\sprintf(
            'Unsupported instance of %s given: %s.',
            Test::class,
            $test::class,
        ));
        // @codeCoverageIgnoreEnd
    }

    /**
     * @throws \InvalidArgumentException
     */
    public static function from(string $id, string $file, int $line = 1): self
    {
        if ('' === $id) {
            throw new \InvalidArgumentException('ID cannot be empty.');
        }

        $file = substr($file, \strlen((string) getcwd()));

        if ('' === $file) {
            throw new \InvalidArgumentException('File path cannot be the current working directory.');
        }

        if ($line <= 0) {
            throw new \InvalidArgumentException('Line cannot be less than 1.');
        }

        return new self($id, $file, $line);
    }

    /**
     * @return non-empty-string
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * @return non-empty-string
     */
    public function file(): string
    {
        return $this->file;
    }

    /**
     * @return int<1, max>
     */
    public function line(): int
    {
        return $this->line;
    }
}
