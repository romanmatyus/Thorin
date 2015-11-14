<?php

namespace RM\Thorin;

use Nette\Object;
use Nette\Utils\Random;
use RM\Thorin\IModeStorage;

/**
 * Service offering generate images using modules.
 *
 * @author Roman Mátyus
 * @copyright (c) Roman Mátyus 2015
 * @license MIT
 */
class Thorin extends Object
{
	/** @var IMode */
	private $defaultMode;

	/** @var ModeGenerator */
	private $modeGenerator;

	/** @var IModeStorage */
	private $modeStorage;

	/** @var IRouter[] */
	private $routers = [];

	function __construct(IMode $defaultMode, ModeGenerator $modeGenerator, IModeStorage $modeStorage, IRouter $router)
	{
		$this->modeGenerator = $modeGenerator;
		$this->modeStorage = $modeStorage;
		$this->addRouter($router);
		$this->setDefaultMode($defaultMode);
	}

	public function addRouter(IRouter $router)
	{
		$this->routers[] = $router;
	}

	function setDefaultMode(IMode $defaultMode)
	{
		$defaultMode->setName('default');
		$this->defaultMode = $defaultMode;
		$this->modeStorage
			->removeMode('default')
			->addMode($defaultMode);
		return $this;
	}

	/**
	 * Add IMode into service.
	 * @param IMode  $mode
	 */
	public function addMode($mode)
	{
		$this->modeStorage->addMode($mode);
		return $this;
	}

	/**
	 * Add IMode into service, only if not exists.
	 * @param IMode  $mode
	 */
	public function addModeOnlyNew(IMode $mode)
	{
		try {
			$this->modeStorage->addMode($mode);
		} catch (InvalidArgumentException $e) {}
		return $this;
	}

	public function createMode($args)
	{
		$this->addMode($this->modeGenerator->generate($args));
		return $this->modeStorage->getMode($args['name']);
	}

	public function createDescriptor($source, $mode = NULL)
	{

		if (is_string($mode)) {
			$mode = $this->getMode($mode);
		} elseif (!$mode instanceof IMode&&$mode !== NULL) {
			throw new InvalidArgumentException("Parameter \$mode must be name of declared Mode, instance of IMode or NULL");
		} elseif ($mode === NULL) {
			$mode = $this->defaultMode;
		}
		return new Descriptor($source, $mode);
	}

	public function getMode($name)
	{
		return $this->modeStorage->getMode($name);
	}

	public function getDescriptorFromLink($link)
	{
		foreach ($this->routers as $router) {
			$descriptor = $router->getDescriptor($link);
			if ($descriptor instanceof Descriptor) {
				return $descriptor;
			}
		}
	}

	public function getLinkFromSource($source, $mode = NULL)
	{
		if ($mode === NULL)
			$mode = $this->defaultMode;
		return (string) $this->createDescriptor($source, $mode);
	}
}
