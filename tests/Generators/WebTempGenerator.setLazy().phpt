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

$generator = new WebTempGenerator(__DIR__ . "/../temp", TRUE, $router);

Assert::type(WebTempGenerator::class, $generator->setLazy());

Assert::type(WebTempGenerator::class, $generator->setLazy(TRUE));

Assert::type(WebTempGenerator::class, $generator->setLazy(FALSE));

Assert::exception(function () use ($generator) {
	$generator->setLazy(NULL);
}, InvalidArgumentException::class, "First argument '\$lazy' must be 'boolean', not 'NULL'.");

Assert::exception(function () use ($generator) {
	$generator->setLazy(new StdClass);
}, InvalidArgumentException::class, "First argument '\$lazy' must be 'boolean', not 'object'.");
