<?php

namespace RM\Thorin\Modes;

use RM\Thorin\IMode;
use RM\Thorin\Providers\ReturnProvider;
use RM\Thorin\Providers\PathProvider;
use RM\Thorin\Generators\WebTempGenerator;

/**
 * Mode for standard use.
 *
 * @author Roman Mátyus
 * @copyright (c) Roman Mátyus 2015
 * @license MIT
 */
class DefaultMode extends BaseMode
{
	public function __construct(ReturnProvider $returnProvider, PathProvider $pathProvider, WebTempGenerator $generator) {
		parent::__construct('default');
		$this->setProviders([
			$returnProvider,
			$pathProvider,
		]);
		$this->setGenerator($generator);
	}
}
