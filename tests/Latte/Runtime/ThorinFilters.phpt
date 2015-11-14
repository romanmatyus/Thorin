<?php

use Nette\Utils\Image;
use RM\Thorin\Thorin;
use RM\Thorin\Descriptor;
use RM\Thorin\InvalidArgumentException;
use RM\Thorin\Providers\PathProvider;
use RM\Thorin\IMode;
use RM\Thorin\Modes\DefaultMode;
use RM\Thorin\Latte\Runtime\ThorinFilters;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';

$loader = new Nette\DI\ContainerLoader(__DIR__ . '/../../temp', TRUE);
$class = $loader->load('ThorinMacros', function($compiler) {
	$compiler->loadConfig(__DIR__."/config.neon");
});
$container = new $class;

$defaultMode = $container->getByType(DefaultMode::class);
$defaultMode->setProviders([new PathProvider([__DIR__ . '/../..'])]);

$extraMode = clone $defaultMode;
$extraMode->setName('extra');

$thorin = $container->getByType(Thorin::class);
$thorin->setDefaultMode($defaultMode);
$thorin->addMode($extraMode);

$macros = new ThorinFilters($thorin);

$latte = new Latte\Engine;
$latte->setTempDirectory(__DIR__ . '/../../temp');
$latte->addFilter("mode", [$macros, 'mode']);
$latte->addFilter("image", [$macros, 'image']);
$latte->addFilter("resize", [$macros, 'resize']);
Assert::match(
	'<img src="/webtemp/nette-logo-blue.modes-extra|param-%A%.png">
<img src="/webtemp/nette-logo-blue.modes-default|param-%A%.png">
<img src="/webtemp/nette-logo-blue.modes-default-extra|param-%A%.png">
<img src="/webtemp/nette-logo-blue.modes-extra-default|param-%A%.png">
<img src="/webtemp/nette-logo-blue.modes-default|param-%A%|resize-10-10-280-580.png">',
	$latte->renderToString('mode.latte', ['source' => '/assets/nette-logo-blue.png'])
);
