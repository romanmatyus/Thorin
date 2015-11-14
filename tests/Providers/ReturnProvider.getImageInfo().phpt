<?php

use RM\Thorin\Providers\ReturnProvider;
use Tester\Assert;
use Nette\Utils\Image;

require __DIR__ . '/../bootstrap.php';

$provider = new ReturnProvider;
Assert::same(NULL, $provider->getImageInfo('http://placehold.it/350x150'));

$image = Image::fromBlank(1, 1);

Assert::same($image, $provider->getImageInfo($image)["image"]);
Assert::same(md5((string)$image), $provider->getImageInfo($image)["filename"]);

Assert::same(NULL, $provider->getImageInfo(new StdClass));
Assert::same(NULL, $provider->getImageInfo(NULL));
Assert::same(NULL, $provider->getImageInfo(10));
