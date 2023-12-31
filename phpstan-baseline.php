<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$ciRenderer of class Nexus\\\\PHPUnit\\\\Tachycardia\\\\Renderer\\\\RendererQueue constructor expects Nexus\\\\PHPUnit\\\\Tachycardia\\\\Renderer\\\\CiRenderer, Nexus\\\\PHPUnit\\\\Tachycardia\\\\Renderer\\\\Renderer given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/TachycardiaExtension.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
