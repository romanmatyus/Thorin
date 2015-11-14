<?php

namespace RM\Thorin;

/**
 * Interface for Mode storages.
 *
 * @author Roman Mátyus
 * @copyright (c) Roman Mátyus 2015
 * @license MIT
 */
interface IModeStorage {

	/**
	 * Add Mode to storage.
	 * @param IMode  $mode
	 */
	public function addMode(IMode $mode);


	/**
	 * Get Mode from storage by name.
	 * @param  string $name Name of Mode.
	 * @return IMode
	 */
	public function getMode($name);


	/**
	 * Get all Modes.
	 * @return IMode[] Array of all stored Modes.
	 */
	public function getModes();

}
