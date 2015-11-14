<?php

use RM\Thorin\IRouter;
use RM\Thorin\Generators\WebTempGenerator;
use RM\Thorin\InvalidArgumentException;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$loader = new Nette\DI\ContainerLoader(__DIR__ . '/../temp', TRUE);
$class = $loader->load('WebTempGenerator', function($compiler) {
	$compiler->loadConfig(__DIR__."/config.neon");
});
$container = new $class;

$router = $container->getByType(IRouter::class);

Assert::exception(function() use ($router) {
	new WebTempGenerator("undefined", TRUE, $router);
}, InvalidArgumentException::class, "Destination 'undefined' not exists.");

Assert::exception(function() use ($router) {
	new WebTempGenerator(__FILE__, TRUE, $router);
}, InvalidArgumentException::class, "Destination '" . __FILE__ . "' must be directory.");

$dir = __DIR__ . "/nonwitable";
@mkdir($dir, 0000);
Assert::exception(function() use ($router, $dir) {
	new WebTempGenerator($dir, TRUE, $router);
}, InvalidArgumentException::class, "Destination '" . $dir . "' must be writable.");
rmdir($dir);

new WebTempGenerator(__DIR__ . "/../temp", TRUE, $router);
