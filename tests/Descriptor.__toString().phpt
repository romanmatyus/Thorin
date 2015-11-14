<?php

use Nette\Utils\Image;
use RM\Thorin\Descriptor;
use RM\Thorin\InvalidArgumentException;
use RM\Thorin\Providers\PathProvider;
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

$descriptor = new Descriptor(__DIR__ . "/assets/nette-logo-blue.png", $defaultMode);

Assert::type('string', (string)$descriptor);
