<?php

namespace RM\Thorin;

/**
 * Interface for classes that generate URL of Images.
 *
 * @author Roman Mátyus
 * @copyright (c) Roman Mátyus 2015
 * @license MIT
 */
interface IRouter {

	/**
	 * Get usable link for application from Descriptor.
	 * @param  Descriptor $descriptor
	 * @return string
	 */
	function getLink(Descriptor $descriptor);

	/**
	 * Generate Descriptor from link generated by method getLink().
	 * @param  string $link
	 * @return Descriptor
	 */
	function getDescriptor($link);
}
