<?php

use Nette\Utils\Image;
use RM\Thorin\Descriptor;
use RM\Thorin\InvalidArgumentException;
use RM\Thorin\FileNotFoundException;
use RM\Thorin\IProvider;
use Tester\Assert;

require __DIR__ . '/bootstrap.php';

class StringProvider implements IProvider
{
	function getImageInfo($source)
	{
		if (is_string($source))
			return [
				"image" => Image::fromBlank(50,50),
				"filename" => "image",
			];
	}
	public function getSource($filename, array $params = NULL) {}
}

class IntegerProvider implements IProvider
{
	function getImageInfo($source)
	{
		if (is_int($source))
			return [
				"image" => Image::fromBlank($source, $source),
				"filename" => $source."x".$source,
			];
	}
	public function getSource($filename, array $params = NULL) {}
}

$descriptor = new Descriptor;
$descriptor->setProviders([
	new StringProvider,
	new IntegerProvider,
]);

$descriptor->setSource('/path/ot/image');
Assert::same(50, $descriptor->getImage()->getHeight());
Assert::same("image", $descriptor->getOutputFilename());

$descriptor->setSource(10);
Assert::same(10, $descriptor->getImage()->getHeight());
Assert::same("10x10", $descriptor->getOutputFilename());

Assert::exception(function() use ($descriptor) {
	$descriptor->setProviders([]);
	$descriptor->setSource("undefined.png");
	$descriptor->getImage();
}, FileNotFoundException::class, "Providers not able provide Image from 'undefined.png'.");
