--TEST--
Test using annotations
--FILE--
<?php

declare(strict_types=1);

$old = getenv('GITHUB_ACTIONS');
putenv('GITHUB_ACTIONS');

$_SERVER['argv'][] = 'tests/end-to-end/using-annotations/AnnotationsTest.php';
$_SERVER['argv'][] = '--no-coverage';

require_once __DIR__.'/../../../vendor/autoload.php';
(new PHPUnit\TextUI\Application)->run($_SERVER['argv']);

putenv("GITHUB_ACTIONS={$old}");
--EXPECTF--
PHPUnit %s by Sebastian Bergmann and contributors.

Runtime: %s
Configuration: %s
Random Seed:   %s

..                                                                  2 / 2 (100%)

Nexus\PHPUnit\Tachycardia\TachycardiaExtension identified this sole slow test:
⚠  Took %fs from 2.0000s limit to run Nexus\\PHPUnit\\Tachycardia\\Tests\\EndToEnd\\UsingAnnotations\\AnnotationsTest::testSlowTestUsesClassTimeLimit


Time: %s, Memory: %f MB

OK (2 tests, 2 assertions)
