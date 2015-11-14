<?php

use RM\Thorin\Modes\BaseMode;
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

$mode = new BaseMode("provider");
$mode->setProviders([new PathProvider([__DIR__ . '/'])]);

$thorin = $container->getByType(Thorin::class);

$descriptor = $thorin->createDescriptor("../assets/nette-logo-blue.png");
$descriptor->addMode($mode);
$descriptor->namespace = 'image/big/';
$descriptor->addModifier('crop', [10, 10, $descriptor->getImage()->getWidth() - 20, $descriptor->getImage()->getHeight() - 20]);
$descriptor->addModifier('crop', [20, 30, 300, 400]);
$descriptor->resize(100, 100);

Assert::match("/webtemp/image/big/nette-logo-blue.modes-default-provider|%A%|%A%|crop-10-10-580-280|crop-20-30-300-400|resize-100-100.png", $container->getByType(SimpleRouter::class)->getLink($descriptor));
