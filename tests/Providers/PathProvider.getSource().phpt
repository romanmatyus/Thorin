<?php

use Nette\Utils\Image;
use RM\Thorin\InvalidArgumentException;
use RM\Thorin\Providers\PathProvider;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$provider = new PathProvider([__DIR__ . '/']);

$source = '../assets/nette-logo-blue.png';

$imageInfo = $provider->getImageInfo($source);

Assert::same($source, $provider->getSource($imageInfo['filename'], $imageInfo['params']));
Assert::same(NULL, $provider->getSource($imageInfo['filename']));
