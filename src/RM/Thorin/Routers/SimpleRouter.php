<?php

namespace RM\Thorin\Routers;

use Nette\Object;
use RM\Thorin\Descriptor;
use RM\Thorin\IModeStorage;
use RM\Thorin\InvalidArgumentException;
use RM\Thorin\IRouter;

/**
 * Router for bidirectional generating Link's and Descriptor's.
 *
 * @author Roman Mátyus
 * @copyright (c) Roman Mátyus 2015
 * @license MIT
 */
class SimpleRouter extends Object implements IRouter
{
	/** @var string */
	protected $outputPath;

	/** @var IModeStorage */
	protected $modeStorage;

	public function __construct($outputPath, IModeStorage $modeStorage)
	{
		$this->outputPath = $outputPath;
		$this->modeStorage = $modeStorage;
	}


	/**
	 * Generate link from configured Descriptor.
	 * @param  Descriptor $descriptor
	 * @return string
	 */
	public function getLink(Descriptor $descriptor)
	{
		return $this->outputPath . '/'
			. $descriptor->namespace
			. implode(".", array_filter([
				$descriptor->getOutputFilename(),
				$this->getParamsFromDescriptor($descriptor),
				$this->getExtension($descriptor),
			]));
	}


	/**
	 * Link parser for rear-generation Description.
	 * @param  string $link
	 * @return RM\Thorin\Descriptor
	 */
	public function getDescriptor($link)
	{
		$filename = basename($link);
		$tmp = explode('.', basename($link));

		$ext = array_pop($tmp);
		$arguments = array_pop($tmp);
		$filename = implode('.', $tmp);

		if (!isset($arguments))
			return;

		$arguments = explode('|', $arguments);

		if (substr($arguments[0], 0, 6) !== 'modes-')
			return;

		$modes = array_reverse(explode('-', substr(array_shift($arguments), 6)));

		$params = [];
		$modifiers = [];
		foreach ($arguments as $value) {
			$tmp = explode('-', $value);
			if (substr($value, 0, 6) === 'param-') {
				$tmp = explode('-', base64_decode($tmp[1]));
				$params[$tmp[0]] = $tmp[1];
			} else {
				$name = array_shift($tmp);
				$modifiers[] = [$name => $tmp];
			}
		}

		foreach ($modes as $mode) {
			$mode = $this->modeStorage->getMode($mode);

			foreach ($mode->getProviders() as $provider) {
				$source = $provider->getSource($filename, $params);
				if ($source !== NULL) {
					$descriptor = new Descriptor($source, $mode);
					foreach ($modifiers as $modifier) {
						foreach ($modifier as $name => $args) {
							$descriptor->addModifier($name, $args);
						}
					}
				}
			}

			if (isset($descriptor))
				return $descriptor;
		}
	}


	/**
	 * @return string
	 */
	public function getOutputPath()
	{
		return $this->outputPath;
	}


	/**
	 * Generator of parameters section in path.
	 * @param  Descriptor $descriptor
	 * @return string
	 */
	protected function getParamsFromDescriptor(Descriptor $descriptor)
	{
		$modes = [];
		foreach ($descriptor->getModes() as $mode) {
			$modes[] = $mode->getName();
		}

		$params = [];
		foreach ($descriptor->getProviderParams() as $name => $value) {
			$params[] = 'param-' . base64_encode($name . '-' . $value);
		}

		$modificators = [];
		foreach ($descriptor->getModifiers() as $modificator) {
			foreach ($modificator as $name => $args) {
				$tmp = $name;

				if (!empty($args))
					$tmp .= "-".implode("-", $args);

				$modificators[] = $tmp;
			}
		}

		return (!empty($modes) OR !empty($modificators))
			? implode(
				'|',
				array_filter(
					array_merge(
						(!empty($modes)) ? ['modes-' . implode('-', $modes)] : [],
						(!empty($params)) ? $params : [],
						(!empty($modificators)) ? $modificators : []
					)
				)
			)
			: NULL;
	}


	/**
	 * Extension generator.
	 * @param  Descriptor $descriptor
	 * @return string
	 */
	protected function getExtension(Descriptor $descriptor)
	{
		switch (strtolower(pathinfo($descriptor->getOutputFilename(), PATHINFO_EXTENSION))) {
			case 'jpg':
			case 'jpeg':
				return 'jpg';
			case 'gif':
				return 'gif';
			default:
				return 'png';
		}
	}
}
