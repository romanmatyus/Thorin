<?php

use RM\Thorin\ModeStorages\DefaultStorage;
use RM\Thorin\Modes\BaseMode;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$storage = new DefaultStorage;

Assert::exception(function() use ($storage) {
	$storage->getMode(NULL);
}, InvalidArgumentException::class, "Parameter '\$name' must be string.");

Assert::exception(function() use ($storage) {
	$storage->getMode('undefined');
}, InvalidArgumentException::class, "Mode %a% is not defined.");

$storage->addMode(new BaseMode('default'));
$storage->getMode('default');
