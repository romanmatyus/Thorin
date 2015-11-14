<?php

namespace RM\Thorin\Modes;

use Nette\Object;
use RM\Thorin\IGenerator;
use RM\Thorin\IMode;
use RM\Thorin\InvalidArgumentException;
use RM\Thorin\IProvider;

/**
 * Base class for Modes.
 *
 * @author Roman Mátyus
 * @copyright (c) Roman Mátyus 2015
 * @license MIT
 */
class BaseMode extends Object implements IMode
{
	/** @var string */
	protected $name;

	/** @var array */
	protected $modifiers = [];

	/** @var IProvider[] */
	protected $providers;

	/** @IGenerator */
	protected $generator;

	/** @var string */
	protected $namespace;


	public function __construct($name = NULL) {
		$this->setName($name);
	}


	/**
	 * Set name of Mode.
	 * @param string $name
	 * @return self
	 */
	public function setName($name)
	{
		if (!is_string($name) AND !is_null($name))
			throw new InvalidArgumentException("Parameter '\$name' must be string");

		$this->name = $name;
		return $this;
	}


	/**
	 * Set namespace of generated images.
	 * @param string $namespace
	 * @return self
	 */
	public function setNamespace($namespace)
	{
		if (!is_string($namespace))
			throw new InvalidArgumentException("Parameter '\$namespace' must be string.");

		$this->namespace = $namespace;
		return $this;
	}


	/**
	 * Add modifier to Mode.
	 * @param string $name Name of mode
	 * @param array $args [description]
	 */
	public function addModifier($name, $args = NULL)
	{
		if (!is_string($name))
			throw new InvalidArgumentException("Parameter '\$name' must be string.");
		if (!is_array($args) AND !is_null($args))
			throw new InvalidArgumentException("Parameter '\$args' must be array.");

		$this->modifiers[][$name] = (is_array($args)) ? $args : [];
		return $this;
	}


	public function addProvider($provider)
	{
		if ($provider instanceof IProvider||is_callable($provider))
			$this->providers[] = $provider;
		else
			throw new InvalidArgumentException("Parameter \$provider is not instance of IProvider or callable.");
		return $this;
	}


	public function setProviders(array $providers)
	{
		foreach ($providers as $provider)
			$this->addProvider($provider);
		return $this;
	}


	public function setGenerator(IGenerator $generator)
	{
		$this->generator = $generator;
		return $this;
	}


	public function getName()
	{
		return $this->name;
	}


	public function getNamespace()
	{
		return $this->namespace;
	}


	public function getProviders()
	{
		return $this->providers;
	}


	public function getGenerator()
	{
		return $this->generator;
	}


	public function getModifiers()
	{
		return $this->modifiers;
	}

}
