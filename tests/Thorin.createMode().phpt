<?php

use Nette\Utils\Image;
use RM\Thorin\Thorin;
use RM\Thorin\IProvider;
use RM\Thorin\IGenerator;
use RM\Thorin\IMode;
use RM\Thorin\InvalidArgumentException;
use RM\Thorin\Descriptor;
use Tester\Assert;

require __DIR__ . '/bootstrap.php';

$loader = new Nette\DI\ContainerLoader(__DIR__ . '/temp', TRUE);
$class = $loader->load('', function($compiler) {
	$compiler->loadConfig(__DIR__.'/config.neon');
});
$container = new $class;

$thorin = $container->getByType(Thorin::class);

Assert::type(IMode::class, $thorin->createMode(['name' => 'testMode']));

Assert::exception(function() use ($thorin) {
	$thorin->createMode(['name' => 'testMode']);
}, InvalidArgumentException::class, "Mode 'testMode' already exists.");

class TestGenerator implements IGenerator {
	function getLink(Descriptor $descriptor) {}
	function getImage($filename) {}
}

class TestProvider implements IProvider {
	function getImageInfo($source) {}
	function getSource($filename, array $params = NULL) {}
}

$args = [
	'name' => 'testMode',
	'providers' => [
		new TestProvider,
		new TestProvider,
	],
	'modifiers' => [
		['resize' => [100, 200]],
		['sharpen' => NULL],
		['place' => [Image::fromBlank(100, 100), 0, 0, 90]]
	],
	'generator' => new TestGenerator,
];

Assert::exception(function() use ($thorin, $args) {
	$thorin->createMode($args);
}, InvalidArgumentException::class, "Mode 'testMode' already exists.");

$args2 = $args;
$args2['name'] = 'testMode2';

$mode = $thorin->createMode($args2);

$args['modifiers'][1] = ['sharpen' => []];

Assert::same($args['providers'], $mode->getProviders());
Assert::same($args['modifiers'], $mode->getModifiers());
Assert::same($args['generator'], $mode->getGenerator());
