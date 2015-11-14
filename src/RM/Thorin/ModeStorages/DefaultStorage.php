<?php

namespace RM\Thorin\ModeStorages;

use Nette\Object;
use RM\Thorin\IMode;
use RM\Thorin\InvalidArgumentException;
use RM\Thorin\IModeStorage;
use RM\Thorin\IProvider;
use RM\Thorin\IGenerator;
use RM\Thorin\Descriptor;
use Nette\Utils\Image;

/**
 * Storage of defined Modes.
 *
 * @author Roman Mátyus
 * @copyright (c) Roman Mátyus 2015
 * @license MIT
 */
class DefaultStorage extends Object implements IModeStorage
{
	/** @var array */
	protected $modes = [];


	/**
	 * Add Mode to storage.
	 * @param string $name Name of Mode.
	 * @param IMode  $mode
	 */
	public function addMode(IMode $mode)
	{
		if (strlen($mode->getName())<1)
			throw new InvalidArgumentException("Name of Mode must be not empty.");
		elseif (isset($this->modes[$mode->getName()]))
			throw new InvalidArgumentException("Mode '" . $mode->getName() . "' already exists.");

		$this->modes[$mode->getName()] = $mode;
		return $mode;
	}


	/**
	 * Get Mode from storage by name.
	 * @param  string $name Name of Mode.
	 * @return IMode
	 */
	public function getMode($name)
	{
		if (!is_string($name)) {
			throw new InvalidArgumentException("Parameter '\$name' must be string.");
		} elseif (!isset($this->modes[$name])) {
			throw new InvalidArgumentException("Mode '$name' is not defined.");
		}
		return $this->modes[$name];
	}


	/**
	 * Get all Modes.
	 * @return IMode[] Array of all stored Modes.
	 */
	public function getModes()
	{
		return $this->modes;
	}

	/**
	 * Remove Mode.
	 * @param string $name
	 */
	public function removeMode($mode)
	{
		unset($this->modes[$mode]);
		return $this;
	}
}
