<?php

use RM\Thorin\ModeStorages\DefaultStorage;
use RM\Thorin\Modes\BaseMode;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$storage = new DefaultStorage;

$mode = new BaseMode('default');

$storage->addMode($mode);
Assert::same(['default' => $mode], $storage->getModes());
