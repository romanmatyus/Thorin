<?php

use Nette\Utils\Image;
use RM\Thorin\Descriptor;
use RM\Thorin\InvalidArgumentException;
use RM\Thorin\IProvider;
use Tester\Assert;

require __DIR__ . '/bootstrap.php';

$image = new Descriptor;
Assert::type(Descriptor::class, $image->addModifier("resize", [100, 200, Image::FIT]));

Assert::type(Descriptor::class, $image->addModifier("colorAt", [100, 200]));

Assert::exception(function () use ($image) {
	$image->addModifier("undefinedMethod", [100, 200, Image::FIT]);
}, InvalidArgumentException::class, "Modifier must be method of Nette\\Utils\\Image, not undefinedMethod()");
