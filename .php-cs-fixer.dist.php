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

use Nexus\CsConfig\Factory;
use Nexus\CsConfig\Ruleset\Nexus81;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->files()
    ->in([
        __DIR__.'/src',
        __DIR__.'/tests',
    ])
    ->append([__FILE__])
;

$overrides = [];
$options = [
    'finder' => $finder,
    'cacheFile' => 'build/.php-cs-fixer.cache',
];

return Factory::create(new Nexus81(), $overrides, $options)->forLibrary(
    'Nexus Tachycardia',
    'John Paul E. Balandan, CPA',
    'paulbalandan@gmail.com',
    2021,
);
