<?php

use Nette\Utils\Image;
use RM\Thorin\Descriptor;
use RM\Thorin\InvalidArgumentException;
use RM\Thorin\IProvider;
use RM\Thorin\IMode;
use RM\Thorin\Modes\DefaultMode;
use RM\Thorin\Modes\BaseMode;
use Tester\Assert;

require __DIR__ . '/bootstrap.php';

$loader = new Nette\DI\ContainerLoader(__DIR__ . '/temp', TRUE);
$class = $loader->load('Descriptor', function($compiler) {
	$compiler->loadConfig(__DIR__."/config.neon");
});
$container = new $class;

$defaultMode = $container->getByType(DefaultMode::class);

$cropMode = new BaseMode;
$cropMode->addModifier('crop', [10, 10, 100, 100]);

$resizeMode = new BaseMode;
$resizeMode->addModifier('resize', [50, 50]);

$descriptor = new Descriptor(__FILE__ . "/assets/nette-logo-blue.png");
$descriptor->addMode($cropMode);
$descriptor->addMode($resizeMode);

foreach ($descriptor->getModes() as $mode)
	Assert::type(IMode::class, $mode);

Assert::same([], $descriptor->getModifiers());
