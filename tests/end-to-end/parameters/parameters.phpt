--TEST--
Test using custom parameters
--FILE--
<?php

declare(strict_types=1);

$old = getenv('GITHUB_ACTIONS');
putenv('GITHUB_ACTIONS');

$_SERVER['argv'][] = 'tests/end-to-end/parameters/AnnotationsTest.php';
$_SERVER['argv'][] = '--configuration';
$_SERVER['argv'][] = 'tests/end-to-end/parameters/phpunit.xml.dist';

require_once __DIR__.'/../../../vendor/autoload.php';
(new PHPUnit\TextUI\Application)->run($_SERVER['argv']);

putenv("GITHUB_ACTIONS={$old}");
--EXPECTF--
PHPUnit %s by Sebastian Bergmann and contributors.

Runtime: %s
Configuration: %s
Random Seed:   %s

.                                                                   1 / 1 (100%)

Nexus\PHPUnit\Tachycardia\TachycardiaExtension identified this sole slow test:
+-----------------------------------------------------------------------------------------------------------+---------------+-------------+
| Test Case                                                                                                 | Time Consumed | Time Limit  |
+-----------------------------------------------------------------------------------------------------------+---------------+-------------+
| Nexus\\PHPUnit\\Tachycardia\\Tests\\EndToEnd\\Parameters\\AnnotationsTest::testSlowTestUsesClassTimeLimit | %s   | 00:00:00.50 |
+-----------------------------------------------------------------------------------------------------------+---------------+-------------+

Slow tests: Time: %s (%f%%)

Time: %s, Memory: %f MB

OK (1 test, 1 assertion)
