<?php

use RM\Thorin\Thorin;
use RM\Thorin\Providers\PathProvider;
use RM\Thorin\IGenerator;
use RM\Thorin\IProvider;
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

class FakeProvider implements IProvider
{
	function getImageInfo($source)
	{
		if (file_exists(__DIR__ . "/" . $source))
			return [
				"image" => Image::fromFile(__DIR__ . "/" . $source),
				"filename" => basename($source),
			];
	}
	public function getSource($filename, array $params = NULL) {}
}

$defaultMode = $container->getByType(DefaultMode::class);
$defaultMode->setProviders([new FakeProvider]);

$thorin = $container->getByType(Thorin::class);
$thorin->setDefaultMode($defaultMode);
$generator = $container->getByType(WebTempGenerator::class);

### 1. Test ###
$descriptor = $thorin->createDescriptor("../assets/nette-logo-blue.png");

Assert::type(Descriptor::class, $descriptor);
Assert::type(Image::class, $descriptor->getImage());

Assert::match("/webtemp/nette-logo-blue.modes-default%A%.png", $generator->getLink($descriptor));
