<?php

namespace RM\Thorin;

use Nette\Object;

/**
 * Class generating Mode's.
 *
 * @author Roman Mátyus
 * @copyright (c) Roman Mátyus 2015
 * @license MIT
 */
class ModeGenerator extends Object
{
	/** @var IMode */
	private $baseMode;

	public function __construct(IMode $baseMode)
	{
		$this->setBaseMode($baseMode);
	}

	/**
	 * Set base Mode for generating new Modes.
	 * @param  IMode $baseMode
	 * @return self
	 */
	public function setBaseMode(IMode $baseMode)
	{
		$this->baseMode = $baseMode;
		return $this;
	}

	/**
	 * Generate Mode from array.
	 * @param  array $args
	 * @return IMode
	 */
	public function generate($args)
	{
		$mode = clone $this->baseMode;
		$mode->setName($args["name"]);

		if (isset($args["namespace"]))
			$mode->setNamespace($args["namespace"]);

		if (isset($args["generator"]))
			$mode->setGenerator($args["generator"]);

		if (isset($args["providers"]))
			$mode->setProviders($args["providers"]);

		if (isset($args["modifiers"])) {
			foreach ($args["modifiers"] as $modifier) {
				if (is_array($modifier)) {
					foreach ($modifier as $n => $args) {
						$mode->addModifier($n, $args);
					}
				} elseif (is_string($modifier)) {
					$mode->addModifier($modifier);
				}
			}
		}

		return $mode;
	}
}
