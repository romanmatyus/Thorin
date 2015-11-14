<?php

use Nette\Utils\Image;
use RM\Thorin\IGenerator;
use RM\Thorin\InvalidArgumentException;
use RM\Thorin\IProvider;
use RM\Thorin\Modes\BaseMode;
use RM\Thorin\Thorin;
use RM\Thorin\Descriptor;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

class TestGenerator implements IGenerator {
	function getLink(Descriptor $descriptor) {}
	function getImage($filename) {}
}

class TestProvider implements IProvider {
	function getImageInfo($source) {}
	public function getSource($filename, array $params = NULL) {}
}

$name = "default";

$namespace = "/article/big";

$providers = [
	new TestProvider,
	new TestProvider,
];

$modifiers = [
	["resize" => [100, 200]],
	["sharpen" => NULL],
	["place" => [Image::fromBlank(100, 100), 0, 0, 90]],
];

$generator = new TestGenerator;

$mode = new BaseMode($name);
$mode->setNamespace($namespace)
	->setProviders($providers)
	->setGenerator($generator);

foreach ($modifiers as $modifier)
	foreach ($modifier as $n => $args)
		$mode->addModifier($n, $args);

$modifiers[1]['sharpen'] = [];

Assert::same($name, $mode->getName());
Assert::same($namespace, $mode->getNamespace());
Assert::same($providers, $mode->getProviders());
Assert::same($modifiers, $mode->getModifiers());
Assert::same($generator, $mode->getGenerator());

Assert::exception(function() use ($mode) {
	$mode->addProvider(new StdClass);
}, InvalidArgumentException::class, 'Parameter $provider is not instance of IProvider or callable.');
