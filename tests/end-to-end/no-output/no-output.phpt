--TEST--
Test with no output
--FILE--
<?php

declare(strict_types=1);

$_SERVER['argv'][] = 'tests/end-to-end/using-annotations/AnnotationsTest.php';
$_SERVER['argv'][] = '--no-coverage';
$_SERVER['argv'][] = '--no-output';

require_once __DIR__.'/../../../vendor/autoload.php';
(new PHPUnit\TextUI\Application)->run($_SERVER['argv']);
--EXPECTF--
