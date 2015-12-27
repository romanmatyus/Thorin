<?php

namespace RM\Thorin;

use Nette\Object;
use Nette\Utils\Image;

/**
 * Class for storing all information about image.
 *
 * @author Roman Mátyus
 * @copyright (c) Roman Mátyus 2015
 * @license MIT
 */
class Descriptor extends Object
{

	/** @var mixed */
	protected $source;

	/** @var array of ['name of function' => [arg1, arg2, ...]] */
	protected $modifiers = [];

	/** @var IMode[] */
	protected $modes;

	/** @var array of IProvider */
	protected $providers = [];

	/** @var IGenerator */
	protected $generator;

	/** @var string */
	protected $namespace;

	/** @var string */
	protected $outputFilename;

	/** @var bool It's possible lazy loading using Provider */
	protected $lazy = FALSE;

	/** @var [] Informations for rear-generation of Images from link */
	protected $providerParams = array();

	public function __construct($source = NULL, IMode $mode = NULL)
	{
		$this->source = $source;
		if (!is_null($mode))
			$this->setModes([$mode]);
	}

	public function getSource()
	{
		return $this->source;
	}

	public function getOutputFilename()
	{
		if (empty($this->outputFilename))
			$this->getImage();
		return $this->outputFilename;
	}

	public function getImage()
	{
		foreach ($this->providers as $provider) {
			$i = $provider->getImageInfo($this->source);

			if ($i["image"] instanceof Image) {
				$this->outputFilename = (string) $i["filename"];
				if (isset($i["lazy"]))
					$this->lazy = $i["lazy"];
				if (isset($i["params"]))
					$this->providerParams = $i["params"];
				return $i["image"];
			}
		}
		throw new FileNotFoundException("Providers not able provide Image from '" . $this->source . "'.");
	}

	public function getGeneratedImage()
	{
		$image = $this->getImage();
		foreach ($this->modes as $mode)
			foreach ($mode->getModifiers() as $modifier)
				foreach ($modifier as $name => $args)
					call_user_func_array([$image, $name], $args);

		foreach ($this->modifiers as $modifier)
			foreach ($modifier as $name => $args)
				call_user_func_array([$image, $name], $args);

		return $image;
	}

	public function getNamespace()
	{
		return $this->namespace;
	}

	public function setNamespace($namespace)
	{
		if (!is_string($namespace))
			throw new InvalidArgumentException("Parameter \$namespace must be string");
		$this->namespace = $namespace;
		return $this;
	}

	public function setModes(array $modes)
	{
		foreach ($modes as $mode)
			$this->addMode($mode);
		return $this;
	}

	public function addMode(IMode $mode)
	{
		if (!empty($mode->getNamespace()))
			$this->setNamespace($mode->getNamespace());

		if (!empty($mode->getProviders()))
			$this->setProviders($mode->getProviders());

		if (!empty($mode->getGenerator()))
		$this->setGenerator($mode->getGenerator());

		$this->modes[] = $mode;
		return $this;
	}

	public function getModes()
	{
		return $this->modes;
	}

	public function addModifier($name, $args = NULL)
	{
		if (!method_exists(Image::class, $name) AND !function_exists('image' . $name))
			throw new InvalidArgumentException('Modifier must be method of '.Image::class.', not '.$name.'()');
		$this->modifiers[][$name] = $args;
		return $this;
	}

	public function setSource($source)
	{
		$this->source = $source;
		return $this;
	}

	public function setProviders(array $providers)
	{
		$this->providers = [];
		foreach ($providers as $provider)
			$this->addProvider($provider);
		return $this;
	}

	public function addProvider($provider)
	{
		if ($provider instanceof IProvider)
			$this->providers[] = $provider;
		else
			throw new InvalidArgumentException("Parameter \$provider must be instance of " . IProvider::class);
	}

	function setGenerator(IGenerator $generator)
	{
		$this->generator = $generator;
	}

	public function getModifiers()
	{
		return $this->modifiers;
	}

	public function getProviderParams()
	{
		return $this->providerParams;
	}

	public function isLazy()
	{
		return $this->lazy;
	}

	public function __toString()
	{
		try {
			return $this->generator->getLink($this);
		} catch (\Exception $e) {
			trigger_error($e->getMessage(), E_USER_ERROR);
			return '';
		}
	}

	public function __call($name, $args)
	{
		$this->addModifier($name, $args);
	}
}
