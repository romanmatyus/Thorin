<?php

use Nette\Utils\Image;
use RM\Thorin\InvalidArgumentException;
use RM\Thorin\Providers\PathProvider;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$provider = new PathProvider([__DIR__ . '/']);
Assert::type(Image::class, $provider->getImageInfo('../assets/nette-logo-blue.png')["image"]);
Assert::match("nette-logo-blue", $provider->getImageInfo('../assets/nette-logo-blue.png')["filename"]);

Assert::same(NULL, $provider->getImageInfo(new StdClass));
Assert::same(NULL, $provider->getImageInfo(NULL));
Assert::same(NULL, $provider->getImageInfo(10));

mkdir(__DIR__ . "/nonreadable", 0000);
Assert::exception(function() {
	$provider = new PathProvider([__DIR__, __DIR__ . "/nonreadable"]);
}, InvalidArgumentException::class, 'Dir %a% is not readable.');
rmdir(__DIR__ . "/nonreadable");
