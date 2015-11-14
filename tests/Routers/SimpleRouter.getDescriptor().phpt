<?php

use Nette\Utils\Image;
use RM\Thorin\Descriptor;
use RM\Thorin\Modes\DefaultMode;
use RM\Thorin\Providers\PathProvider;
use RM\Thorin\Routers\SimpleRouter;
use RM\Thorin\Thorin;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$loader = new Nette\DI\ContainerLoader(__DIR__ . '/../temp', TRUE);
$class = $loader->load('WebTempGenerator', function($compiler) {
	$compiler->loadConfig(__DIR__."/config.neon");
});
$container = new $class;

$defaultMode = $container->getByType(DefaultMode::class);
$defaultMode->setProviders([new PathProvider([__DIR__ . '/'])]);

$thorin = $container->getByType(Thorin::class);

$router = $container->getByType(SimpleRouter::class);

$descriptor = $router->getDescriptor($router->getLink($thorin->createDescriptor("../assets/nette-logo-blue.png")));

Assert::type(Descriptor::class, $descriptor);
Assert::type(Image::class, $descriptor->getImage());

Assert::same(NULL, $router->getDescriptor("undefined.modes-default.png"));
