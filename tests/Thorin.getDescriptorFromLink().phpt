<?php

use RM\Thorin\Thorin;
use RM\Thorin\IProvider;
use RM\Thorin\Descriptor;
use RM\Thorin\IRouter;
use Tester\Assert;
use Nette\Utils\Image;
use RM\Thorin\InvalidArgumentException;
use Nette\Caching\Storages\DevNullStorage;

require __DIR__ . '/bootstrap.php';

$loader = new Nette\DI\ContainerLoader(__DIR__ . '/temp', TRUE);
$class = $loader->load('', function($compiler) {
	$compiler->loadConfig(__DIR__."/config.neon");
});
$container = new $class;

class FakeRouter implements IRouter
{
	function getLink(Descriptor $image) {}
	function getDescriptor($link) {
		return new Descriptor("xxx");
	}
}

$thorin = $container->getByType(Thorin::class);

Assert::same(NULL, $thorin->getDescriptorFromLink("anything"));

$thorin->addRouter(new FakeRouter);

Assert::type(Descriptor::class, $thorin->getDescriptorFromLink("anything"));
