<?php

use RM\Thorin\Thorin;
use RM\Thorin\Providers\PathProvider;
use RM\Thorin\IGenerator;
use RM\Thorin\Generators\WebTempGenerator;
use RM\Thorin\Modes\DefaultMode;
use Tester\Assert;
use Nette\Utils\Image;
use RM\Thorin\Descriptor;
use RM\Thorin\InvalidArgumentException;

require __DIR__ . '/../bootstrap.php';

$loader = new Nette\DI\ContainerLoader(__DIR__ . '/../temp', TRUE);
$class = $loader->load('WebTempGenerator', function($compiler) {
	$compiler->loadConfig(__DIR__."/config.neon");
});
$container = new $class;

$defaultMode = $container->getByType(DefaultMode::class);
$defaultMode->setProviders([new PathProvider([__DIR__ . '/'])]);

$thorin = $container->getByType(Thorin::class);

$generator = $container->getByType(WebTempGenerator::class);
$generator->setLazy(FALSE);

### 1. Test ###

$descriptor = $thorin->createDescriptor("../assets/nette-logo-blue.png");
$descriptor->setNamespace('image/article/big/');
$descriptor->resize(200, 200);

$link = $generator->getLink($descriptor);
$image = $generator->getImage($link);

Assert::type(Image::class, $image);
Assert::same(200, $image->getWidth());
Assert::same(100, $image->getHeight());

### 2. Test ###
$descriptor = $thorin->createDescriptor("../assets/nette-logo-blue.png");

$generator->onCompile[] = function(Image $image) {
	return $image->resize(300, 300);
};

$generator->onAfterRender[] = function($filePath) {
	$image = Image::fromFile($filePath);
	$image->setPixel(0, 0, $image->colorAllocate(255, 0, 0));
	$image->save($filePath);
};

$link = $generator->getLink($descriptor);
$image = $generator->getImage($link);

Assert::type(Image::class, $image);
Assert::same(300, $image->getWidth());
Assert::same(150, $image->getHeight());

$rgb = $image->colorAt(0, 0);
Assert::same(255, ($rgb >> 16) & 0xFF);
Assert::same(0, ($rgb >> 8) & 0xFF);
Assert::same(0, $rgb & 0xFF);

### 3. Test ###
Assert::same(NULL, $generator->getImage("xxx"));
