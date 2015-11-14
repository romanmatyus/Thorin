<?php

use Nette\Utils\Image;
use RM\Thorin\Descriptor;
use RM\Thorin\InvalidArgumentException;
use RM\Thorin\IProvider;
use Tester\Assert;

require __DIR__ . '/bootstrap.php';

class FakeProvider implements IProvider
{
	function getImageInfo($source)
	{
		return Image::fromBlank(100,100);
	}
	public function getSource($filename, array $params = NULL) {}
}

$descriptor = new Descriptor;

Assert::type(Descriptor::class, $descriptor->setProviders([
		new FakeProvider,
	])
);

Assert::exception(function () use ($descriptor) {
	$descriptor->setProviders([
		new StdClass,
	]);
}, InvalidArgumentException::class, 'Parameter $provider must be instance of '.IProvider::class);
