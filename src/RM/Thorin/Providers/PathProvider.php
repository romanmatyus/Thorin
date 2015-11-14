<?php

namespace RM\Thorin\Providers;

use Nette\Utils\Image;
use Nette\Utils\Validators;
use RM\Thorin\IProvider;
use RM\Thorin\InvalidArgumentException;
use Nette\Object;

/**
 * Provider of Images from files.
 *
 * @author Roman Mátyus
 * @copyright (c) Roman Mátyus 2015
 * @license MIT
 */
class PathProvider extends Object implements IProvider
{
	private $dirs = [''];

	public function __construct(array $dirs = NULL)
	{
		if (!empty($dirs)) {
			foreach ($dirs as $dir) {
				$this->addDir($dir);
			}
		}
	}

	/**
	 * Adding folders in where will be searched images.
	 * @param string $dir
	 * @return self
	 */
	public function addDir($dir)
	{
		if (!file_exists($dir))
			throw new InvalidArgumentException("Path '" . $dir . "' not exists.");
		elseif (!is_dir($dir))
			throw new InvalidArgumentException("Path ".realpath($dir)." is not dir");
		elseif (!is_readable($dir))
			throw new InvalidArgumentException("Dir ".realpath($dir)." is not readable.");
		else
			$this->dirs[] = $dir;

		$this->dirs = array_merge(array_filter(array_unique($this->dirs)), ['']);

		return $this;
	}


	/**
	 * Get Image from source
	 * @param  mixed $source Source of image
	 * @return array|NULL ["image" => Nette\Utils\Image, "filename" => string]
	 */
	public function getImageInfo($source)
	{
		if (is_string($source)) {
			foreach ($this->dirs as $dir) {
				if (file_exists($dir . $source)) {
					$path_parts = pathinfo($source);
					$filename = $path_parts['filename'];
					return [
						"image" => Image::fromFile($dir . $source),
						"filename" => $filename,
						"lazy" => TRUE,
						"params" => [
							"dir" => $path_parts['dirname'],
							"ext" => $path_parts['extension'],
						]
					];
				} elseif (Validators::isUrl($source)) {
					return [
						"image" => Image::fromFile($source),
						"filename" => basename($source),
					];
				}
			}
		}
	}

	/**
	 * Get source from filename and parameters.
	 * @param  string $filename
	 * @param  array $params
	 * @return string
	 */
	public function getSource($filename, array $params = NULL)
	{
		if (isset($params["dir"]) && isset($params["ext"]))
			return $params["dir"] . "/" . $filename . "." . $params["ext"];
	}
}
