<?php

namespace RM\Thorin\Providers;

use Nette\Utils\Image;
use RM\Thorin\IProvider;
use Nette\Object;

/**
 * Provider of Images from Image class.
 *
 * @author Roman Mátyus
 * @copyright (c) Roman Mátyus 2015
 * @license MIT
 */
class ReturnProvider extends Object implements IProvider
{
	/**
	 * Get Image from source
	 * @param  mixed $source Source of image
	 * @return array|NULL ["image" => Nette\Utils\Image, "filename" => string]
	 */
	public function getImageInfo($source)
	{
		if (is_object($source)) {
			if (get_class($source) === Image::class) {
				return [
					"image" => $source,
					"filename" => md5((string)$source),
				];
			}
		}
	}


	/**
	 * Get source from filename and parameters.
	 * @param  string $filename
	 * @param  array $params
	 * @return string
	 */
	public function getSource($filename, array $params = NULL) {}
}
