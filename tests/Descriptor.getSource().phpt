<?php

use RM\Thorin\Descriptor;
use Tester\Assert;

require __DIR__ . '/bootstrap.php';

$descriptor = new Descriptor(__DIR__ . "/assets/nette-logo-blue.png");
Assert::same(__DIR__ . "/assets/nette-logo-blue.png", $descriptor->getSource());

$descriptor = new Descriptor;
Assert::same(NULL, $descriptor->getSource());

$descriptor = new Descriptor;
$descriptor->setSource(new StdClass);
Assert::type(StdClass::class, $descriptor->getSource());
