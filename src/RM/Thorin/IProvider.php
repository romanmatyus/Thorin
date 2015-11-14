<?php

namespace RM\Thorin;

/**
 * Interface for Image providers.
 *
 * @author Roman Mátyus
 * @copyright (c) Roman Mátyus 2015
 * @license MIT
 */
interface IProvider {
	/**
	 * Get Image from source
	 * @param  mixed $source Source of image
	 * @return array|NULL ["image" => Nette\Utils\Image, "filename" => string]
	 */
	public function getImageInfo($source);

	/**
	 * Get source from filename and parameters.
	 * @param  string $filename
	 * @param  array $params
	 * @return string
	 */
	public function getSource($filename, array $params = NULL);
}
