<?php

use Nette\Utils\Image;
use RM\Thorin\Descriptor;
use RM\Thorin\InvalidArgumentException;
use RM\Thorin\IProvider;
use RM\Thorin\Modes\BaseMode;
use Tester\Assert;

require __DIR__ . '/bootstrap.php';

class FakeProvider implements IProvider
{
	function getImageInfo($source)
	{
		return [
			"image" => Image::fromFile(__DIR__ . "/" . $source),
			"filename" => basename($source),
		];
	}
	public function getSource($filename, array $params = NULL) {}
}

class FakeMode extends BaseMode
{
	function __construct() {
		$this->setName("fake");
		$this->addModifier("resize", [500, 500]);
	}
}

$descriptor = new Descriptor("assets/nette-logo-blue.png");
$descriptor->addProvider(new FakeProvider);

Assert::same(300, $descriptor->getImage()->getHeight());
Assert::same(600, $descriptor->getImage()->getWidth());

$descriptor->addMode(new FakeMode);
$image = $descriptor->getGeneratedImage();

Assert::same(250, $image->getHeight());
Assert::same(500, $image->getWidth());

$descriptor->addModifier("resize", [200, 200]);
$image = $descriptor->getGeneratedImage();

Assert::same(100, $image->getHeight());
Assert::same(200, $image->getWidth());

$descriptor->addModifier("resize", [100, 100, Image::STRETCH]);
$image = $descriptor->getGeneratedImage();

Assert::same(100, $image->getHeight());
Assert::same(100, $image->getWidth());
