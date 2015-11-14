<?php

use RM\Thorin\Thorin;
use RM\Thorin\IProvider;
use RM\Thorin\IGenerator;
use RM\Thorin\IMode;
use Tester\Assert;
use Nette\Utils\Image;
use RM\Thorin\InvalidArgumentException;
use Nette\Caching\Storages\DevNullStorage;

require __DIR__ . '/bootstrap.php';

class Mode implements IMode
{
	protected $name;
	function __construct($name) { $this->setName($name); }
	function setName($name) { $this->name = $name; }
	function setNamespace($namespace) {}
	function setProviders(array $providers) {}
	function setGenerator(IGenerator $generator) {}
	function addModifier($name, $args = NULL) {}
	function getName() { return $this->name; }
	function getNamespace() {}
	function getProviders() {}
	function getGenerator() {}
	function getModifiers() {}
}

$mode = new Mode('testMode');

$loader = new Nette\DI\ContainerLoader(__DIR__ . '/temp', TRUE);
$class = $loader->load('', function($compiler) {
	$compiler->loadConfig(__DIR__ . '/config.neon');
});
$container = new $class;

$thorin = $container->getByType(Thorin::class);

Assert::same($thorin, $thorin->addMode($mode));
Assert::same($mode, $thorin->getMode('testMode'));

Assert::exception(function() use ($thorin, $mode) {
	$thorin->addMode($mode);
}, InvalidArgumentException::class, "Mode 'testMode' already exists.");

$mode->setName('');
Assert::exception(function() use ($thorin, $mode) {
	$thorin->addMode($mode);
}, InvalidArgumentException::class, "Name of Mode must be not empty.");
