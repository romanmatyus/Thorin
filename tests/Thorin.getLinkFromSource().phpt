<?php

use RM\Thorin\Thorin;
use RM\Thorin\Providers\PathProvider;
use RM\Thorin\IGenerator;
use RM\Thorin\Modes\DefaultMode;
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
$mode = $container->getByType(DefaultMode::class);
$mode->setProviders([new PathProvider([__DIR__ . '/'])]);

Assert::match("/webtemp/nette-logo-blue.modes-default|%A%|%A%.png", $thorin->getLinkFromSource('assets/nette-logo-blue.png', $mode));
