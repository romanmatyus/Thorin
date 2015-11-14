<?php

use RM\Thorin\Thorin;
use RM\Thorin\IProvider;
use RM\Thorin\IGenerator;
use RM\Thorin\Modes\BaseMode;
use Tester\Assert;
use RM\Thorin\Descriptor;
use RM\Thorin\InvalidArgumentException;

require __DIR__ . '/bootstrap.php';

$loader = new Nette\DI\ContainerLoader(__DIR__ . '/temp', TRUE);
$class = $loader->load('', function($compiler) {
	$compiler->loadConfig(__DIR__."/config.neon");
});
$container = new $class;

$thorin = $container->getByType(Thorin::class);
$thorin->createMode(['name' => 'testMode']);

$source = "/path/to/file";
$mode = new BaseMode;

$image = $thorin->createDescriptor($source, $mode);
Assert::type(Descriptor::class, $image);

$image = $thorin->createDescriptor($source, "testMode");
Assert::type(Descriptor::class, $image);

$image = $thorin->createDescriptor($source);
Assert::type(Descriptor::class, $image);

Assert::exception(function() use ($thorin, $source) {
	$thorin->createDescriptor($source, new StdClass);
}, InvalidArgumentException::class, 'Parameter $mode must be name of declared Mode, instance of IMode or NULL');
