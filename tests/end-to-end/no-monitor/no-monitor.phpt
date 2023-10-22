--TEST--
Test with no output
--FILE--
<?php

declare(strict_types=1);

putenv('TACHYCARDIA_MONITOR=disabled');
putenv('TACHYCARDIA_MONITOR_GA=disabled');

$_SERVER['argv'][] = 'tests/end-to-end/using-annotations/AnnotationsTest.php';
$_SERVER['argv'][] = '--no-coverage';

require_once __DIR__.'/../../../vendor/autoload.php';
(new PHPUnit\TextUI\Application)->run($_SERVER['argv']);

putenv('TACHYCARDIA_MONITOR');
putenv('TACHYCARDIA_MONITOR_GA');
--EXPECTF--
PHPUnit %s by Sebastian Bergmann and contributors.

Runtime: %s
Configuration: %s
Random Seed:   %s

..                                                                  2 / 2 (100%)

Time: %s, Memory: %f MB

OK (2 tests, 2 assertions)
