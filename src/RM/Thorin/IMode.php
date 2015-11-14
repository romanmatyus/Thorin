<?php

namespace RM\Thorin;

/**
 * Interface of all Modes.
 *
 * @author Roman Mátyus
 * @copyright (c) Roman Mátyus 2015
 * @license MIT
 */
interface IMode {
	function setName($name);
	function setNamespace($namespace);
	function setProviders(array $providers);
	function setGenerator(IGenerator $generator);
	function addModifier($name, $args = NULL);
	function getName();
	function getNamespace();
	function getProviders();
	function getGenerator();
	function getModifiers();
}
