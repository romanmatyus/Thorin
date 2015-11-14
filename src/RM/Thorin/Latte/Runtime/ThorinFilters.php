<?php

namespace RM\Thorin\Latte\Runtime;

use Nette\Object;
use RM\Thorin\Descriptor;
use RM\Thorin\IMode;
use RM\Thorin\Thorin;

/**
 * List of Latte filters for simple use Thorin.
 *
 * @author Roman Mátyus
 * @copyright (c) Roman Mátyus 2015
 * @license MIT
 */
class ThorinFilters extends Object
{
	/** @var Thorin */
	private $thorin;

	public function __construct(Thorin $thorin)
	{
		$this->thorin = $thorin;
	}


	/**
	 * Filter to obtain the Descriptor.
	 * @param  mixed $source Source of Descriptor
	 * @return Descriptor
	 */
	public function image($source)
	{
		return $this->getDescriptor($source);
	}


	/**
	 * Apply Mode to Descriptor.
	 * @param  mixed  $source Source of Descriptor
	 * @param  string $mode   Name of Mode
	 * @return Descriptor
	 */
	public function mode($source, $mode)
	{
		return $this->getDescriptor($source, $this->thorin->getMode($mode));
	}


	/**
	 * Apply resizing to Descriptor.
	 * @param  mixed  $source Source of Descriptor
	 * @return Descriptor
	 */
	public function resize($source)
	{
		$args = func_get_args();
		array_shift($args);
		return $this->getDescriptor($source)->addModifier(__FUNCTION__, $args);
	}


	/**
	 * Get Descriptor from source.
	 * @param  mixed $source Source of Descriptor
	 * @param  IMode $mode
	 * @return Descriptor
	 */
	private function getDescriptor($source, IMode $mode = NULL)
	{
		if ($source instanceof Descriptor) {
			return ($mode === NULL)
				? $source
				: $source->addMode($mode);
		} else {
			return $this->thorin->createDescriptor($source, $mode);
		}
	}
}
